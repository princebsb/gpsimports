<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class CheckoutController extends BaseController
{
    protected $cartService;
    protected $orderService;
    protected $paymentService;

    public function __construct()
    {
        $this->cartService = service('cart');
        $this->orderService = service('order');
        $this->paymentService = service('payment');
    }

    public function index()
    {
        if (!session()->get('customer_logged_in')) {
            return redirect()->to('/login?redirect=' . urlencode('/checkout'));
        }

        $validation = $this->cartService->validateForCheckout();

        if (!$validation['valid']) {
            return redirect()->to('/carrinho')->with('errors', $validation['errors']);
        }

        $cart = $validation['cart'];

        // Validar valor minimo
        $minSubtotal = 300.00;
        if ((float)($cart['subtotal'] ?? 0) < $minSubtotal) {
            return redirect()->to('/carrinho')->with('error', 'O valor minimo para compra e de R$ ' . number_format($minSubtotal, 2, ',', '.') . '.');
        }
        $customerId = session()->get('customer_id');
        $customer = model('CustomerModel')->find($customerId);
        $addresses = model('CustomerAddressModel')->getByCustomer($customerId);
        $paymentMethods = $this->paymentService->getAvailableMethods();
        $installments = $this->paymentService->getInstallmentOptions($cart['total']);

        // Mercado Pago public key
        $mpPublicKey = (new \App\Libraries\Payment\MercadoPago())->getPublicKey();

        return view('front/checkout/index', [
            'title' => 'Finalizar Compra',
            'cart' => $cart,
            'customer' => $customer,
            'addresses' => $addresses,
            'paymentMethods' => $paymentMethods,
            'installments' => $installments,
            'mpPublicKey' => $mpPublicKey,
        ]);
    }

    public function process()
    {
        if (!session()->get('customer_logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sessao expirada.']);
        }

        $data = $this->request->getPost();
        $customerId = session()->get('customer_id');
        $customer = model('CustomerModel')->find($customerId);

        // Validar carrinho
        $cart = $this->cartService->getCurrentCart();

        // Validar valor minimo
        $minSubtotal = 300.00;
        if ((float)($cart['subtotal'] ?? 0) < $minSubtotal) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'O valor minimo para compra e de R$ ' . number_format($minSubtotal, 2, ',', '.') . '.',
            ]);
        }

        // Validar se o metodo de envio foi selecionado
        if (empty($cart['shipping_method']) || (float)($cart['shipping_cost'] ?? 0) <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Selecione um metodo de envio antes de finalizar.',
            ]);
        }

        // Get shipping address
        if (!empty($data['use_new_address']) && $data['use_new_address'] === '1') {
            // Novo endereco
            $data['shipping_zipcode'] = $data['new_zipcode'] ?? '';
            $data['shipping_street'] = $data['new_street'] ?? '';
            $data['shipping_number'] = $data['new_number'] ?? '';
            $data['shipping_complement'] = $data['new_complement'] ?? '';
            $data['shipping_neighborhood'] = $data['new_neighborhood'] ?? '';
            $data['shipping_city'] = $data['new_city'] ?? '';
            $data['shipping_state'] = $data['new_state'] ?? '';

            // Salvar novo endereco para o cliente
            $addressModel = model('CustomerAddressModel');
            $isFirstAddress = $addressModel->where('customer_id', $customerId)->countAllResults() === 0;

            $addressData = [
                'customer_id' => $customerId,
                'name' => 'Endereco de Entrega',
                'recipient' => $customer['name'] ?? 'Destinatario',
                'zipcode' => preg_replace('/\D/', '', $data['shipping_zipcode']),
                'street' => $data['shipping_street'],
                'number' => $data['shipping_number'],
                'complement' => $data['shipping_complement'] ?? '',
                'neighborhood' => $data['shipping_neighborhood'],
                'city' => $data['shipping_city'],
                'state' => $data['shipping_state'],
                'is_default' => $isFirstAddress ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Inserir diretamente para evitar problemas de validacao
            $db = \Config\Database::connect();
            $db->table('customer_addresses')->insert($addressData);
        } else {
            // Endereco salvo
            $addressId = $data['address_id'] ?? null;
            if ($addressId) {
                $address = model('CustomerAddressModel')->find($addressId);
                if ($address && $address['customer_id'] == $customerId) {
                    $data['shipping_zipcode'] = $address['zipcode'];
                    $data['shipping_street'] = $address['street'];
                    $data['shipping_number'] = $address['number'];
                    $data['shipping_complement'] = $address['complement'];
                    $data['shipping_neighborhood'] = $address['neighborhood'];
                    $data['shipping_city'] = $address['city'];
                    $data['shipping_state'] = $address['state'];
                }
            }
        }
        $data['shipping_name'] = $customer['name'] ?? '';
        $data['shipping_phone'] = $customer['phone'] ?? $customer['mobile'] ?? '';
        $data['billing_cpf'] = $customer['cpf'] ?? '';

        // Create order
        $orderResult = $this->orderService->createFromCart($data);

        if (!$orderResult['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode(' ', $orderResult['errors']),
            ]);
        }

        $order = $orderResult['order'];

        // Criar preferencia do Checkout Pro
        $checkoutProResult = $this->paymentService->createCheckoutPro($order['id']);

        if (!$checkoutProResult['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $checkoutProResult['message'] ?? 'Erro ao criar pagamento.',
            ]);
        }

        // Limpar carrinho
        service('cart')->clearCart();

        // Atualizar estatisticas do cliente
        model('CustomerModel')->updateOrderStats($customerId, $order['total']);

        return $this->response->setJSON([
            'success' => true,
            'order_number' => $order['order_number'],
            'redirect' => $checkoutProResult['init_point'],
        ]);
    }

    public function success($orderNumber)
    {
        $customerId = session()->get('customer_id');
        $order = $this->orderService->getOrderForCustomer($orderNumber, $customerId);

        if (!$order) {
            return redirect()->to('/minha-conta/pedidos');
        }

        return view('front/checkout/success', [
            'title' => 'Pedido Confirmado',
            'order' => $order,
        ]);
    }

    public function pending($orderNumber)
    {
        $customerId = session()->get('customer_id');
        $order = $this->orderService->getOrderForCustomer($orderNumber, $customerId);

        if (!$order) {
            return redirect()->to('/minha-conta/pedidos');
        }

        // Get payment data manually to avoid JSON cast issues
        $db = \Config\Database::connect();
        $payment = $db->table('payments')
                      ->where('order_id', $order['id'])
                      ->orderBy('created_at', 'DESC')
                      ->get()
                      ->getRowArray();

        return view('front/checkout/pending', [
            'title' => 'Aguardando Pagamento',
            'order' => $order,
            'payment' => $payment,
        ]);
    }

    public function failure($orderNumber)
    {
        $customerId = session()->get('customer_id');
        $order = $this->orderService->getOrderForCustomer($orderNumber, $customerId);

        if (!$order) {
            return redirect()->to('/minha-conta/pedidos');
        }

        return view('front/checkout/failure', [
            'title' => 'Pagamento Recusado',
            'order' => $order,
        ]);
    }
}
