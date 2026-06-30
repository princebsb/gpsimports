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

        return view('admin/orders/index', [
            'title' => 'Pedidos',
            'orders' => $result['orders'],
            'pager' => $result['pager'],
            'filters' => $filters,
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
}
