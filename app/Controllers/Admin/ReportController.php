<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ReportController extends BaseController
{
    public function index()
    {
        return view('admin/reports/index', [
            'title' => 'Relatorios',
        ]);
    }

    public function sales()
    {
        $startDate = $this->request->getGet('start') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end') ?? date('Y-m-d');

        $db = \Config\Database::connect();

        $sales = $db->table('orders')
            ->select('DATE(created_at) as date, COUNT(*) as total_orders, SUM(total) as total_revenue')
            ->where('payment_status', 'approved')
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();

        $totals = $db->table('orders')
            ->selectSum('total', 'revenue')
            ->selectCount('id', 'orders')
            ->where('payment_status', 'approved')
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();

        return view('admin/reports/sales', [
            'title' => 'Relatorio de Vendas',
            'sales' => $sales,
            'totals' => $totals,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function products()
    {
        $db = \Config\Database::connect();

        $products = $db->table('order_items')
            ->select('order_items.product_id, products.name, products.sku, SUM(order_items.quantity) as total_sold, SUM(order_items.subtotal) as total_revenue')
            ->join('products', 'products.id = order_items.product_id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.payment_status', 'approved')
            ->groupBy('order_items.product_id')
            ->orderBy('total_sold', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        return view('admin/reports/products', [
            'title' => 'Produtos Mais Vendidos',
            'products' => $products,
        ]);
    }

    public function customers()
    {
        $db = \Config\Database::connect();

        $customers = $db->table('orders')
            ->select('customers.id, customers.name, customers.email, COUNT(orders.id) as total_orders, SUM(orders.total) as total_spent')
            ->join('customers', 'customers.id = orders.customer_id')
            ->where('orders.payment_status', 'approved')
            ->groupBy('orders.customer_id')
            ->orderBy('total_spent', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        return view('admin/reports/customers', [
            'title' => 'Melhores Clientes',
            'customers' => $customers,
        ]);
    }

    public function export($type)
    {
        // Simple CSV export based on type
        return redirect()->back()->with('info', 'Exportacao em desenvolvimento.');
    }
}
