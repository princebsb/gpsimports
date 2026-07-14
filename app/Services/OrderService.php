<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\CouponModel;

class OrderService
{
    protected OrderModel $orderModel;
    protected CustomerModel $customerModel;
    protected ProductModel $productModel;
    protected CouponModel $couponModel;
    protected CartService $cartService;
    protected StockService $stockService;
    protected ?EmailService $emailService = null;

    public function __construct()
    {
        $this->orderModel = model('OrderModel');
        $this->customerModel = model('CustomerModel');
        $this->productModel = model('ProductModel');
        $this->couponModel = model('CouponModel');
        $this->cartService = service('cart');
        $this->stockService = service('stock');
    }

    /**
     * Get email service instance (lazy loading)
     */
    protected function getEmailService(): EmailService
    {
        if ($this->emailService === null) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    /**
     * Create order from cart
     */
    public function createFromCart(array $data): array
    {
        // Validate cart
        $validation = $this->cartService->validateForCheckout();

        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        $cart = $validation['cart'];
        $customerId = session()->get('customer_id');

        if (!$customerId) {
            return ['success' => false, 'errors' => ['Voce precisa estar logado para finalizar a compra.']];
        }

        // Calcular desconto PIX (dinamico baseado no valor)
        $pixDiscount = 0;
        $totalWithPixDiscount = $cart['total'];
        if (($data['payment_method'] ?? '') === 'pix') {
            $pixDiscountPercent = get_pix_discount($cart['total']);
            if ($pixDiscountPercent > 0) {
                $pixDiscount = round($cart['total'] * ($pixDiscountPercent / 100), 2);
                $totalWithPixDiscount = $cart['total'] - $pixDiscount;
            }
        }

        $totalDiscount = $cart['discount'] + $pixDiscount;

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create order
            $orderData = [
                'customer_id' => $customerId,
                'order_number' => $this->orderModel->generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => $cart['subtotal'],
                'discount' => $totalDiscount,
                'pix_discount' => $pixDiscount,
                'shipping_cost' => $cart['shipping_cost'],
                'total' => $totalWithPixDiscount,
                'items_count' => $cart['items_count'],
                'coupon_id' => $cart['coupon_id'],
                'coupon_code' => $data['coupon_code'] ?? null,
                'coupon_discount' => $cart['discount'],
                'payment_method' => $data['payment_method'] ?? 'checkout_pro',
                'payment_gateway' => $data['payment_gateway'] ?? 'mercadopago',
                'installments' => $data['installments'] ?? 1,
                'shipping_method' => $cart['shipping_method'],
                'shipping_name' => $data['shipping_name'],
                'shipping_phone' => $data['shipping_phone'] ?? null,
                'shipping_zipcode' => $data['shipping_zipcode'],
                'shipping_street' => $data['shipping_street'],
                'shipping_number' => $data['shipping_number'],
                'shipping_complement' => $data['shipping_complement'] ?? null,
                'shipping_neighborhood' => $data['shipping_neighborhood'],
                'shipping_city' => $data['shipping_city'],
                'shipping_state' => $data['shipping_state'],
                'billing_name' => $data['billing_name'] ?? $data['shipping_name'],
                'billing_cpf' => $data['billing_cpf'] ?? null,
                'billing_phone' => $data['billing_phone'] ?? $data['shipping_phone'],
                'notes' => $data['notes'] ?? null,
                'ip_address' => service('request')->getIPAddress(),
                'user_agent' => service('request')->getUserAgent()->getAgentString(),
            ];

            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                throw new \Exception('Failed to create order');
            }

            // Add order items
            foreach ($cart['items'] as $item) {
                $this->orderModel->addItem($orderId, [
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'name' => $item['name'],
                    'sku' => $item['sku'],
                    'image' => $item['image'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'attributes' => $item['attributes'],
                    'weight' => $this->productModel->find($item['product_id'])['weight'] ?? 0,
                ]);

                // Reserve stock
                $this->stockService->reserve(
                    $item['product_id'],
                    $item['quantity'],
                    $item['variation_id'],
                    'order',
                    $orderId
                );
            }

            // Record coupon usage
            if ($cart['coupon_id']) {
                $this->couponModel->recordUsage($cart['coupon_id'], $customerId, $orderId, $cart['discount']);
            }

            // Add status history
            $this->orderModel->addStatusHistory($orderId, 'pending', 'Pedido criado');

            // NÃO limpar carrinho aqui - será limpo após pagamento bem sucedido

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            $order = $this->orderModel->find($orderId);

            return [
                'success' => true,
                'order' => $order,
                'message' => 'Pedido criado com sucesso!',
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'OrderService::createFromCart - ' . $e->getMessage());
            return ['success' => false, 'errors' => ['Erro ao criar pedido. Tente novamente.']];
        }
    }

    /**
     * Process payment approval
     */
    public function processPaymentApproval(int $orderId): bool
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            return false;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update order status
            $this->orderModel->updateStatus($orderId, 'paid', 'Pagamento aprovado', true);
            $this->orderModel->update($orderId, ['payment_status' => 'approved']);

            // Confirm stock deduction
            $items = $this->orderModel->getItems($orderId);
            foreach ($items as $item) {
                $this->stockService->confirmReservation(
                    $item['product_id'],
                    $item['quantity'],
                    $item['variation_id']
                );

                // Update sales count
                $this->productModel->incrementSales($item['product_id'], $item['quantity']);
            }

            // Calculate and add cashback
            $cashbackPercent = (float) model('SettingModel')->get('cashback_percent', 0);
            if ($cashbackPercent > 0) {
                $cashback = $order['total'] * ($cashbackPercent / 100);
                $this->customerModel->addCashback($order['customer_id'], $cashback);
                $this->orderModel->update($orderId, ['cashback_earned' => $cashback]);
            }

            $db->transComplete();

            // Send confirmation email
            $this->sendOrderConfirmationEmail($orderId);

            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'OrderService::processPaymentApproval - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder(int $orderId, string $reason = ''): bool
    {
        $order = $this->orderModel->find($orderId);

        if (!$order || in_array($order['status'], ['delivered', 'cancelled'])) {
            return false;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update status
            $this->orderModel->updateStatus($orderId, 'cancelled', $reason);

            // Release stock
            $items = $this->orderModel->getItems($orderId);
            foreach ($items as $item) {
                $this->stockService->releaseReservation(
                    $item['product_id'],
                    $item['quantity'],
                    $item['variation_id'],
                    'order_cancelled',
                    $orderId
                );
            }

            // Reverse coupon usage
            if ($order['coupon_id']) {
                $this->couponModel->set('usage_count', 'usage_count - 1', false)
                                  ->where('id', $order['coupon_id'])
                                  ->update();
            }

            // Reverse cashback if used
            if ($order['cashback_used'] > 0) {
                $this->customerModel->addCashback($order['customer_id'], $order['cashback_used']);
            }

            $db->transComplete();

            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'OrderService::cancelOrder - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status, ?string $comment = null, bool $notifyCustomer = false): bool
    {
        $userId = session()->get('admin_id');

        $result = $this->orderModel->updateStatus($orderId, $status, $comment, $notifyCustomer, $userId);

        if ($result) {
            // Se o status indica que o pedido foi pago, atualizar payment_status
            if (in_array($status, ['paid', 'processing', 'shipped', 'delivered'])) {
                $this->orderModel->update($orderId, ['payment_status' => 'approved']);
            }

            if ($status === 'shipped') {
                $this->orderModel->update($orderId, ['shipped_at' => date('Y-m-d H:i:s')]);
            }

            if ($status === 'delivered') {
                $this->orderModel->update($orderId, ['delivered_at' => date('Y-m-d H:i:s')]);
            }

            if ($notifyCustomer) {
                $this->sendStatusUpdateEmail($orderId, $status);
            }
        }

        return $result;
    }

    /**
     * Add tracking code
     */
    public function addTracking(int $orderId, string $trackingCode, ?string $trackingUrl = null): bool
    {
        $result = $this->orderModel->update($orderId, [
            'tracking_code' => $trackingCode,
            'tracking_url' => $trackingUrl,
        ]);

        if ($result) {
            $this->updateStatus($orderId, 'shipped', 'Codigo de rastreio: ' . $trackingCode, true);
        }

        return $result;
    }

    /**
     * Get order for customer
     */
    public function getOrderForCustomer(string $orderNumber, int $customerId): ?array
    {
        $order = $this->orderModel->getByNumber($orderNumber);

        if (!$order || $order['customer_id'] != $customerId) {
            return null;
        }

        return $this->orderModel->getWithItems($order['id']);
    }

    /**
     * Send order confirmation email
     */
    protected function sendOrderConfirmationEmail(int $orderId): void
    {
        try {
            $order = $this->orderModel->getWithItems($orderId);

            if (!$order) {
                log_message('error', 'OrderService: Order not found for confirmation email - ID: ' . $orderId);
                return;
            }

            $emailService = $this->getEmailService();
            $result = $emailService->sendOrderConfirmationEmail($order);

            if ($result) {
                log_message('info', 'Order confirmation email sent for order #' . $order['order_number']);
            } else {
                log_message('warning', 'Order confirmation email queued for order #' . $order['order_number']);
            }
        } catch (\Exception $e) {
            log_message('error', 'OrderService: Error sending confirmation email - ' . $e->getMessage());
        }
    }

    /**
     * Send status update email
     */
    protected function sendStatusUpdateEmail(int $orderId, string $status): void
    {
        try {
            $order = $this->orderModel->getWithItems($orderId);

            if (!$order) {
                log_message('error', 'OrderService: Order not found for status email - ID: ' . $orderId);
                return;
            }

            $emailService = $this->getEmailService();
            $result = $emailService->sendOrderStatusEmail($order, $status);

            if ($result) {
                log_message('info', 'Status update email sent for order #' . $order['order_number'] . ' - Status: ' . $status);
            } else {
                log_message('warning', 'Status update email queued for order #' . $order['order_number'] . ' - Status: ' . $status);
            }
        } catch (\Exception $e) {
            log_message('error', 'OrderService: Error sending status email - ' . $e->getMessage());
        }
    }
}
