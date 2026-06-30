<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $orderModel = model('OrderModel');
        $customerModel = model('CustomerModel');
        $productModel = model('ProductModel');

        // Get statistics
        $todayStats = $orderModel->getStats('today');
        $monthStats = $orderModel->getStats('month');

        // Recent orders
        $recentOrders = $orderModel->select('orders.*, customers.name as customer_name')
            ->join('customers', 'customers.id = orders.customer_id')
            ->orderBy('orders.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Stock alerts
        $stockAlerts = service('stock')->getAlerts(true);

        // Counts
        $pendingOrders = $orderModel->where('status', 'pending')->countAllResults();
        $totalCustomers = $customerModel->countAllResults();
        $totalProducts = $productModel->where('status', 'active')->countAllResults();
        $lowStockCount = count(service('stock')->getLowStockProducts(100));

        return view('admin/dashboard/index', [
            'title' => 'Dashboard',
            'todayStats' => $todayStats,
            'monthStats' => $monthStats,
            'recentOrders' => $recentOrders,
            'stockAlerts' => $stockAlerts,
            'pendingOrders' => $pendingOrders,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
        ]);
    }

    public function login()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login', [
            'title' => 'Admin Login',
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = model('UserModel');
        $user = $userModel->verifyCredentials(
            $this->request->getPost('email'),
            $this->request->getPost('password')
        );

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email ou senha invalidos.');
        }

        // Set session
        session()->set([
            'admin_logged_in' => true,
            'admin_id' => $user['id'],
            'admin_name' => $user['name'],
            'admin_email' => $user['email'],
            'admin_role_id' => $user['role_id'],
        ]);

        // Log action
        model('AuditLogModel')->log('admin_login', 'User', $user['id']);

        return redirect()->to('/admin/dashboard')->with('success', 'Bem-vindo, ' . $user['name'] . '!');
    }

    public function logout()
    {
        model('AuditLogModel')->log('admin_logout', 'User', session()->get('admin_id'));

        session()->remove(['admin_logged_in', 'admin_id', 'admin_name', 'admin_email', 'admin_role_id']);

        return redirect()->to('/admin/login')->with('success', 'Voce saiu do sistema.');
    }

    public function profile()
    {
        $userModel = model('UserModel');
        $user = $userModel->find(session()->get('admin_id'));

        return view('admin/profile/index', [
            'title' => 'Meu Perfil',
            'user' => $user,
        ]);
    }

    public function updateProfile()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email',
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = model('UserModel');
        $userId = session()->get('admin_id');

        // Check if email is taken by another user
        $existing = $userModel->where('email', $this->request->getPost('email'))
            ->where('id !=', $userId)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Este email ja esta em uso.');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($userId, $data);

        // Update session
        session()->set([
            'admin_name' => $data['name'],
            'admin_email' => $data['email'],
        ]);

        return redirect()->to('/admin/perfil')->with('success', 'Perfil atualizado com sucesso!');
    }
}
