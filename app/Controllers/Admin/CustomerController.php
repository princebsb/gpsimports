<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class CustomerController extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = model('CustomerModel');
    }

    public function index()
    {
        $search = $this->request->getGet('search');

        $builder = $this->customerModel->orderBy('created_at', 'DESC');

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }

        $customers = $builder->paginate(20);

        return view('admin/customers/index', [
            'title' => 'Clientes',
            'customers' => $customers,
            'pager' => $this->customerModel->pager,
            'search' => $search,
        ]);
    }

    public function show($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return redirect()->to('/admin/clientes')->with('error', 'Cliente nao encontrado.');
        }

        $orders = model('OrderModel')
            ->where('customer_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $addresses = model('CustomerAddressModel')
            ->where('customer_id', $id)
            ->findAll();

        return view('admin/customers/show', [
            'title' => $customer['name'],
            'customer' => $customer,
            'orders' => $orders,
            'addresses' => $addresses,
        ]);
    }

    public function toggleStatus($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return $this->response->setJSON(['success' => false]);
        }

        $newStatus = $customer['status'] === 'active' ? 'inactive' : 'active';
        $this->customerModel->update($id, ['status' => $newStatus]);

        return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
    }

    public function export()
    {
        $customers = $this->customerModel->findAll();

        $filename = 'clientes_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Nome', 'Email', 'Telefone', 'Status', 'Criado em']);

        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer['id'],
                $customer['name'],
                $customer['email'],
                $customer['phone'],
                $customer['status'],
                $customer['created_at'],
            ]);
        }

        fclose($output);
        exit;
    }
}
