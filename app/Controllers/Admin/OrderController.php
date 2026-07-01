<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class OrderController extends BaseController
{
    protected $orderModel;
    protected $orderService;

    public function __construct()
    {
        $this->orderModel = model('OrderModel');
        $this->orderService = service('order');
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'payment_status' => $this->request->getGet('payment_status'),
            'from_date' => $this->request->getGet('from_date'),
            'to_date' => $this->request->getGet('to_date'),
        ];

        $result = $this->orderModel->getFiltered($filters, 20);

        // Stats
        $db = \Config\Database::connect();
        $stats = [
            'pending' => $db->table('orders')->where('status', 'pending')->where('deleted_at IS NULL')->countAllResults(),
            'processing' => $db->table('orders')->whereIn('status', ['paid', 'processing'])->where('deleted_at IS NULL')->countAllResults(),
            'shipped' => $db->table('orders')->where('status', 'shipped')->where('deleted_at IS NULL')->countAllResults(),
            'delivered' => $db->table('orders')->where('status', 'delivered')->where('deleted_at IS NULL')->countAllResults(),
        ];

        return view('admin/orders/index', [
            'title' => 'Pedidos',
            'orders' => $result['orders'],
            'pager' => $result['pager'],
            'filters' => $filters,
            'stats' => $stats,
            'statuses' => [
                'pending' => 'Pendente',
                'paid' => 'Pago',
                'processing' => 'Em Preparacao',
                'shipped' => 'Enviado',
                'delivered' => 'Entregue',
                'cancelled' => 'Cancelado',
            ],
            'paymentStatuses' => [
                'pending' => 'Aguardando',
                'approved' => 'Aprovado',
                'rejected' => 'Rejeitado',
                'refunded' => 'Reembolsado',
            ],
        ]);
    }

    public function show($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order) {
            return redirect()->to('/admin/pedidos')->with('error', 'Pedido nao encontrado.');
        }

        return view('admin/orders/show', [
            'title' => 'Pedido #' . $order['order_number'],
            'order' => $order,
        ]);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $comment = $this->request->getPost('comment');
        $notifyCustomer = (bool) $this->request->getPost('notify_customer');

        $result = $this->orderService->updateStatus($id, $status, $comment, $notifyCustomer);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? 'Status atualizado!' : 'Erro ao atualizar status.',
            ]);
        }

        if (!$result) {
            return redirect()->back()->with('error', 'Erro ao atualizar status.');
        }

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    public function addTracking($id)
    {
        $trackingCode = $this->request->getPost('tracking_code');
        $trackingUrl = $this->request->getPost('tracking_url');

        if (empty($trackingCode)) {
            return redirect()->back()->with('error', 'Codigo de rastreio obrigatorio.');
        }

        $result = $this->orderService->addTracking($id, $trackingCode, $trackingUrl);

        if (!$result) {
            return redirect()->back()->with('error', 'Erro ao adicionar rastreio.');
        }

        return redirect()->back()->with('success', 'Codigo de rastreio adicionado!');
    }

    public function print($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Pedido nao encontrado.');
        }

        return view('admin/orders/print', [
            'order' => $order,
        ]);
    }

    public function export()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'from_date' => $this->request->getGet('from_date'),
            'to_date' => $this->request->getGet('to_date'),
        ];

        $builder = $this->orderModel->select('orders.*, customers.name as customer_name, customers.email as customer_email')
            ->join('customers', 'customers.id = orders.customer_id');

        if (!empty($filters['status'])) {
            $builder->where('orders.status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $builder->where('orders.created_at >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $builder->where('orders.created_at <=', $filters['to_date'] . ' 23:59:59');
        }

        $orders = $builder->orderBy('orders.created_at', 'DESC')->findAll();

        // Generate CSV
        $filename = 'pedidos_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Header
        fputcsv($output, [
            'Numero',
            'Data',
            'Cliente',
            'Email',
            'Status',
            'Pagamento',
            'Subtotal',
            'Desconto',
            'Frete',
            'Total',
        ], ';');

        // Data
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['order_number'],
                date('d/m/Y H:i', strtotime($order['created_at'])),
                $order['customer_name'],
                $order['customer_email'],
                $this->orderModel->getStatusLabel($order['status']),
                $this->orderModel->getPaymentStatusLabel($order['payment_status']),
                number_format($order['subtotal'], 2, ',', '.'),
                number_format($order['discount'], 2, ',', '.'),
                number_format($order['shipping_cost'], 2, ',', '.'),
                number_format($order['total'], 2, ',', '.'),
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Cotar frete para o pedido
     */
    public function quotarFrete($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Pedido nao encontrado.');
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();

        $package = [
            'weight' => 0.5,
            'height' => 10,
            'width' => 15,
            'length' => 20,
        ];

        $quotes = $melhorEnvio->quoteForOrder($order, $package);

        return $this->response->setJSON([
            'success' => true,
            'quotes' => $quotes,
        ]);
    }

    /**
     * Gerar etiqueta de envio
     */
    public function gerarEtiqueta($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Pedido nao encontrado.');
        }

        $serviceId = (int) $this->request->getPost('service_id');
        $package = [
            'weight' => (float) $this->request->getPost('weight'),
            'height' => (int) $this->request->getPost('height'),
            'width' => (int) $this->request->getPost('width'),
            'length' => (int) $this->request->getPost('length'),
        ];

        if (!$serviceId) {
            return redirect()->back()->with('error', 'Selecione uma transportadora.');
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();

        // 1. Adicionar ao carrinho
        $cartResult = $melhorEnvio->addToCart($order, $package, $serviceId);

        if (!$cartResult['success']) {
            return redirect()->back()->with('error', 'Erro ao adicionar ao carrinho: ' . ($cartResult['message'] ?? 'Erro desconhecido'));
        }

        $cartId = $cartResult['cart_id'];

        // 2. Fazer checkout (pagar)
        $checkoutResult = $melhorEnvio->checkout([$cartId]);

        if (!$checkoutResult['success']) {
            return redirect()->back()->with('error', 'Erro no checkout: ' . ($checkoutResult['message'] ?? 'Erro desconhecido'));
        }

        // 3. Gerar etiqueta
        $labelResult = $melhorEnvio->generateLabel([$cartId]);

        if (!$labelResult['success']) {
            return redirect()->back()->with('error', 'Erro ao gerar etiqueta: ' . ($labelResult['message'] ?? 'Erro desconhecido'));
        }

        // Salvar ID da etiqueta no pedido
        $this->orderModel->update($id, [
            'me_label_id' => $cartId,
        ]);

        // Atualizar status para "Em Preparacao" se ainda estiver pendente
        if (in_array($order['status'], ['pending', 'paid'])) {
            $this->orderService->updateStatus($id, 'processing', 'Etiqueta gerada - Melhor Envio');
        }

        return redirect()->back()->with('success', 'Etiqueta gerada com sucesso!');
    }

    /**
     * Imprimir etiqueta
     */
    public function imprimirEtiqueta($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order || empty($order['me_label_id'])) {
            return redirect()->back()->with('error', 'Etiqueta nao encontrada.');
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();
        $result = $melhorEnvio->printLabel([$order['me_label_id']]);

        if ($result['success'] && !empty($result['url'])) {
            return redirect()->to($result['url']);
        }

        return redirect()->back()->with('error', 'Erro ao imprimir etiqueta.');
    }

    /**
     * Rastrear etiqueta
     */
    public function rastrearEtiqueta($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order || empty($order['me_label_id'])) {
            return redirect()->back()->with('error', 'Etiqueta nao encontrada.');
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();
        $result = $melhorEnvio->tracking([$order['me_label_id']]);

        if ($result['success']) {
            // Retornar JSON com os dados de rastreamento
            return $this->response->setJSON($result['data']);
        }

        return redirect()->back()->with('error', 'Erro ao rastrear.');
    }
}
