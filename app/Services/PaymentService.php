<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Libraries\Payment\MercadoPago;
use App\Libraries\Payment\MercadoPagoCheckoutPro;
use Config\Payment as PaymentConfig;

class PaymentService
{
    protected OrderModel $orderModel;
    protected PaymentModel $paymentModel;
    protected PaymentConfig $config;
    protected ?MercadoPago $gateway = null;

    public function __construct()
    {
        $this->orderModel = model('OrderModel');
        $this->paymentModel = model('PaymentModel');
        $this->config = config('Payment');
    }

    /**
     * Get payment gateway
     */
    protected function getGateway(string $gateway = 'mercadopago'): MercadoPago
    {
        if ($this->gateway === null) {
            $this->gateway = new MercadoPago();
        }

        return $this->gateway;
    }

    /**
     * Process payment
     */
    public function process(int $orderId, string $method, array $data = []): array
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            return ['success' => false, 'message' => 'Pedido nao encontrado.'];
        }

        $gateway = $this->getGateway();

        try {
            switch ($method) {
                case 'pix':
                    return $this->processPix($order, $gateway);

                case 'credit_card':
                    return $this->processCreditCard($order, $gateway, $data);

                case 'debit_card':
                    return $this->processDebitCard($order, $gateway, $data);

                case 'boleto':
                    return $this->processBoleto($order, $gateway);

                default:
                    return ['success' => false, 'message' => 'Metodo de pagamento invalido.'];
            }
        } catch (\Exception $e) {
            log_message('error', 'PaymentService::process - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao processar pagamento.'];
        }
    }

    /**
     * Process PIX payment
     */
    protected function processPix(array $order, MercadoPago $gateway): array
    {
        $result = $gateway->createPixPayment($order);

        if (!$result['success']) {
            return $result;
        }

        // Save payment record
        $paymentId = $this->paymentModel->insert([
            'order_id' => $order['id'],
            'gateway' => 'mercadopago',
            'method' => 'pix',
            'transaction_id' => $result['transaction_id'],
            'external_id' => $result['external_id'] ?? null,
            'status' => 'pending',
            'amount' => $order['total'],
            'pix_qrcode' => $result['qr_code'] ?? null,
            'pix_qrcode_base64' => $result['qr_code_base64'] ?? null,
            'pix_copy_paste' => $result['copy_paste'] ?? null,
            'pix_expiration' => $result['expiration'] ?? null,
            'gateway_response' => json_encode($result['raw_response'] ?? []),
        ]);

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'method' => 'pix',
            'qr_code' => $result['qr_code'],
            'qr_code_base64' => $result['qr_code_base64'],
            'copy_paste' => $result['copy_paste'],
            'expiration' => $result['expiration'],
        ];
    }

    /**
     * Process Credit Card payment
     */
    protected function processCreditCard(array $order, MercadoPago $gateway, array $data): array
    {
        $result = $gateway->createCardPayment($order, $data, 'credit');

        if (!$result['success']) {
            // Save failed payment
            $this->paymentModel->insert([
                'order_id' => $order['id'],
                'gateway' => 'mercadopago',
                'method' => 'credit_card',
                'status' => 'rejected',
                'amount' => $order['total'],
                'installments' => $data['installments'] ?? 1,
                'error_message' => $result['message'],
                'gateway_response' => json_encode($result['raw_response'] ?? []),
            ]);

            return $result;
        }

        // Save payment record
        $paymentId = $this->paymentModel->insert([
            'order_id' => $order['id'],
            'gateway' => 'mercadopago',
            'method' => 'credit_card',
            'transaction_id' => $result['transaction_id'],
            'external_id' => $result['external_id'] ?? null,
            'status' => $result['status'],
            'amount' => $order['total'],
            'installments' => $data['installments'] ?? 1,
            'installment_value' => $order['total'] / ($data['installments'] ?? 1),
            'card_brand' => $result['card_brand'] ?? null,
            'card_last_digits' => $result['card_last_digits'] ?? null,
            'card_holder_name' => $data['card_holder_name'] ?? null,
            'gateway_response' => json_encode($result['raw_response'] ?? []),
        ]);

        // If approved, process order
        if ($result['status'] === 'approved') {
            $this->paymentModel->update($paymentId, ['paid_at' => date('Y-m-d H:i:s')]);
            service('order')->processPaymentApproval($order['id']);
        }

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'method' => 'credit_card',
            'status' => $result['status'],
            'message' => $this->getStatusMessage($result['status']),
        ];
    }

    /**
     * Process Debit Card payment
     */
    protected function processDebitCard(array $order, MercadoPago $gateway, array $data): array
    {
        $result = $gateway->createCardPayment($order, $data, 'debit');

        if (!$result['success']) {
            return $result;
        }

        // Save payment record
        $paymentId = $this->paymentModel->insert([
            'order_id' => $order['id'],
            'gateway' => 'mercadopago',
            'method' => 'debit_card',
            'transaction_id' => $result['transaction_id'],
            'status' => $result['status'],
            'amount' => $order['total'],
            'card_brand' => $result['card_brand'] ?? null,
            'card_last_digits' => $result['card_last_digits'] ?? null,
            'gateway_response' => json_encode($result['raw_response'] ?? []),
        ]);

        if ($result['status'] === 'approved') {
            $this->paymentModel->update($paymentId, ['paid_at' => date('Y-m-d H:i:s')]);
            service('order')->processPaymentApproval($order['id']);
        }

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'method' => 'debit_card',
            'status' => $result['status'],
        ];
    }

    /**
     * Process Boleto payment
     */
    protected function processBoleto(array $order, MercadoPago $gateway): array
    {
        $result = $gateway->createBoletoPayment($order);

        if (!$result['success']) {
            return $result;
        }

        // Save payment record
        $paymentId = $this->paymentModel->insert([
            'order_id' => $order['id'],
            'gateway' => 'mercadopago',
            'method' => 'boleto',
            'transaction_id' => $result['transaction_id'],
            'external_id' => $result['external_id'] ?? null,
            'status' => 'pending',
            'amount' => $order['total'],
            'boleto_url' => $result['boleto_url'] ?? null,
            'boleto_barcode' => $result['barcode'] ?? null,
            'boleto_expiration' => $result['expiration'] ?? null,
            'gateway_response' => json_encode($result['raw_response'] ?? []),
        ]);

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'method' => 'boleto',
            'boleto_url' => $result['boleto_url'],
            'barcode' => $result['barcode'],
            'expiration' => $result['expiration'],
        ];
    }

    /**
     * Handle webhook notification
     */
    public function handleWebhook(string $gateway, array $data): bool
    {
        try {
            $gatewayInstance = $this->getGateway($gateway);
            $paymentData = $gatewayInstance->parseWebhook($data);

            if (!$paymentData) {
                return false;
            }

            $payment = $this->paymentModel->getByExternalId($paymentData['external_id']);

            if (!$payment) {
                $payment = $this->paymentModel->getByTransactionId($paymentData['transaction_id']);
            }

            if (!$payment) {
                log_message('warning', 'Payment not found for webhook: ' . json_encode($paymentData));
                return false;
            }

            // Update payment status
            $this->paymentModel->updatePaymentStatus($payment['id'], $paymentData['status'], $data);

            // Update order
            $this->orderModel->update($payment['order_id'], [
                'payment_status' => $paymentData['status'],
            ]);

            // Process based on status
            if ($paymentData['status'] === 'approved') {
                service('order')->processPaymentApproval($payment['order_id']);
            } elseif (in_array($paymentData['status'], ['rejected', 'cancelled'])) {
                $this->orderModel->updateStatus($payment['order_id'], 'cancelled', 'Pagamento ' . $paymentData['status']);
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'PaymentService::handleWebhook - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get installment options
     */
    public function getInstallmentOptions(float $amount): array
    {
        $maxInstallments = $this->config->methods['credit_card']['max_installments'] ?? 12;
        $minValue = $this->config->methods['credit_card']['min_installment_value'] ?? 50;
        $interestRate = $this->config->methods['credit_card']['interest_rate'] ?? 0;

        $options = [];

        for ($i = 1; $i <= $maxInstallments; $i++) {
            $installmentValue = $amount / $i;

            if ($installmentValue < $minValue && $i > 1) {
                break;
            }

            $total = $amount;
            $interest = 0;

            // Apply interest after N installments (e.g., after 3)
            if ($interestRate > 0 && $i > 3) {
                $interest = $amount * ($interestRate / 100) * ($i - 3);
                $total = $amount + $interest;
                $installmentValue = $total / $i;
            }

            $options[] = [
                'installments' => $i,
                'installment_value' => round($installmentValue, 2),
                'total' => round($total, 2),
                'interest' => round($interest, 2),
                'label' => $i . 'x de R$ ' . number_format($installmentValue, 2, ',', '.') .
                          ($interest > 0 ? ' (com juros)' : ' sem juros'),
            ];
        }

        return $options;
    }

    /**
     * Get available payment methods
     */
    public function getAvailableMethods(): array
    {
        $methods = [];

        foreach ($this->config->methods as $code => $method) {
            if ($method['enabled']) {
                $methods[$code] = $method;
            }
        }

        return $methods;
    }

    /**
     * Get status message
     */
    protected function getStatusMessage(string $status): string
    {
        return $this->config->statusMapping[$status] ?? $status;
    }

    /**
     * Criar preferência do Checkout Pro
     */
    public function createCheckoutPro(int $orderId): array
    {
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return ['success' => false, 'message' => 'Pedido nao encontrado.'];
        }

        $customer = model('CustomerModel')->find($order['customer_id']);
        if (!$customer) {
            return ['success' => false, 'message' => 'Cliente nao encontrado.'];
        }

        // Buscar itens do pedido
        $orderItems = $this->orderModel->getItems($orderId);

        $items = [];
        foreach ($orderItems as $item) {
            $items[] = [
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity' => (int) $item['quantity'],
                'price' => (float) $item['price'],
            ];
        }

        $checkoutPro = new MercadoPagoCheckoutPro();
        $result = $checkoutPro->createPreference($order, $items, $customer);

        if ($result['success']) {
            // Salvar preference_id no pedido
            $this->orderModel->update($orderId, [
                'mp_preference_id' => $result['preference_id'],
            ]);

            // Criar registro de pagamento pendente
            $this->paymentModel->insert([
                'order_id' => $orderId,
                'gateway' => 'mercadopago',
                'method' => 'checkout_pro',
                'status' => 'pending',
                'amount' => $order['total'],
                'mp_preference_id' => $result['preference_id'],
            ]);
        }

        return $result;
    }

    /**
     * Processar webhook do Mercado Pago
     */
    public function processCheckoutProWebhook(array $data): bool
    {
        try {
            $checkoutPro = new MercadoPagoCheckoutPro();
            $paymentData = $checkoutPro->processWebhook($data);

            if (!$paymentData) {
                return false;
            }

            // Buscar pedido pelo external_reference (order_number)
            $order = $this->orderModel->where('order_number', $paymentData['external_reference'])->first();

            if (!$order) {
                log_message('warning', 'Webhook: Pedido nao encontrado - ' . $paymentData['external_reference']);
                return false;
            }

            // Atualizar ou criar registro de pagamento
            $existingPayment = $this->paymentModel->where('order_id', $order['id'])
                                                   ->where('gateway', 'mercadopago')
                                                   ->first();

            $paymentRecord = [
                'order_id' => $order['id'],
                'gateway' => 'mercadopago',
                'method' => $paymentData['payment_type'],
                'transaction_id' => $paymentData['payment_id'],
                'external_id' => $paymentData['payment_id'],
                'status' => $paymentData['status'],
                'amount' => $paymentData['amount'],
                'installments' => $paymentData['installments'],
                'card_brand' => $paymentData['payment_method'],
                'gateway_response' => json_encode($paymentData),
            ];

            if ($paymentData['status'] === 'approved') {
                $paymentRecord['paid_at'] = $paymentData['date_approved'] ?? date('Y-m-d H:i:s');
            }

            if ($existingPayment) {
                $this->paymentModel->update($existingPayment['id'], $paymentRecord);
            } else {
                $this->paymentModel->insert($paymentRecord);
            }

            // Atualizar status do pedido
            $this->orderModel->update($order['id'], [
                'payment_status' => $paymentData['status'],
            ]);

            // Processar aprovacao
            if ($paymentData['status'] === 'approved') {
                service('order')->processPaymentApproval($order['id']);
            } elseif ($paymentData['status'] === 'rejected') {
                $this->orderModel->updateStatus($order['id'], 'cancelled', 'Pagamento rejeitado');
            }

            return true;

        } catch (\Exception $e) {
            log_message('error', 'processCheckoutProWebhook Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Request refund
     */
    public function requestRefund(int $paymentId, ?float $amount = null): array
    {
        $payment = $this->paymentModel->find($paymentId);

        if (!$payment) {
            return ['success' => false, 'message' => 'Pagamento nao encontrado.'];
        }

        if ($payment['status'] !== 'approved') {
            return ['success' => false, 'message' => 'Apenas pagamentos aprovados podem ser reembolsados.'];
        }

        $gateway = $this->getGateway($payment['gateway']);
        $refundAmount = $amount ?? $payment['amount'];

        $result = $gateway->refund($payment['transaction_id'], $refundAmount);

        if ($result['success']) {
            // Record refund
            $db = \Config\Database::connect();
            $db->table('refunds')->insert([
                'order_id' => $payment['order_id'],
                'payment_id' => $paymentId,
                'amount' => $refundAmount,
                'status' => 'approved',
                'external_id' => $result['refund_id'] ?? null,
                'processed_by' => session()->get('admin_id'),
                'processed_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Update payment status
            $this->paymentModel->updatePaymentStatus($paymentId, 'refunded');

            // Update order
            service('order')->updateStatus($payment['order_id'], 'refunded', 'Reembolso processado');
        }

        return $result;
    }
}
