<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class CustomerController extends BaseController
{
    protected $customerService;

    public function __construct()
    {
        $this->customerService = service('customer');
    }

    public function dashboard()
    {
        $customerId = session()->get('customer_id');
        $data = $this->customerService->getDashboardData($customerId);

        return view('front/customer/dashboard', array_merge([
            'title' => 'Minha Conta',
        ], $data));
    }

    public function orders()
    {
        $customerId = session()->get('customer_id');
        $orders = model('OrderModel')->getByCustomer($customerId, 50);

        return view('front/customer/orders', [
            'title' => 'Meus Pedidos',
            'orders' => $orders,
        ]);
    }

    public function orderDetail($orderNumber)
    {
        $customerId = session()->get('customer_id');
        $order = service('order')->getOrderForCustomer($orderNumber, $customerId);

        if (!$order) {
            return redirect()->to('/minha-conta/pedidos')->with('error', 'Pedido nao encontrado.');
        }

        return view('front/customer/order-detail', [
            'title' => 'Pedido #' . $orderNumber,
            'order' => $order,
        ]);
    }

    public function addresses()
    {
        $customerId = session()->get('customer_id');
        $addresses = $this->customerService->getAddresses($customerId);

        return view('front/customer/addresses', [
            'title' => 'Meus Enderecos',
            'addresses' => $addresses,
        ]);
    }

    public function saveAddress()
    {
        $customerId = session()->get('customer_id');
        $data = $this->request->getPost();

        $result = $this->customerService->saveAddress($customerId, $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to('/minha-conta/enderecos')->with('success', $result['message']);
    }

    public function deleteAddress($id)
    {
        $customerId = session()->get('customer_id');
        $result = $this->customerService->deleteAddress($customerId, $id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        return redirect()->to('/minha-conta/enderecos')->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function profile()
    {
        $customerId = session()->get('customer_id');
        $customer = model('CustomerModel')->find($customerId);

        return view('front/customer/profile', [
            'title' => 'Meus Dados',
            'customer' => $customer,
        ]);
    }

    public function updateProfile()
    {
        $customerId = session()->get('customer_id');
        $data = $this->request->getPost();

        $result = $this->customerService->updateProfile($customerId, $data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function password()
    {
        return view('front/customer/password', [
            'title' => 'Alterar Senha',
        ]);
    }

    public function updatePassword()
    {
        $customerId = session()->get('customer_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $result = $this->customerService->updatePassword(
            $customerId,
            $this->request->getPost('current_password'),
            $this->request->getPost('new_password')
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function wishlist()
    {
        $customerId = session()->get('customer_id');
        $wishlist = model('WishlistModel')->getByCustomer($customerId);

        return view('front/customer/wishlist', [
            'title' => 'Meus Favoritos',
            'wishlist' => $wishlist,
        ]);
    }

    public function addToWishlist($productId)
    {
        $customerId = session()->get('customer_id');
        $result = model('WishlistModel')->toggle($customerId, $productId);

        return $this->response->setJSON([
            'success' => true,
            'action' => $result['action'],
            'in_wishlist' => $result['in_wishlist'],
        ]);
    }

    public function removeFromWishlist($productId)
    {
        $customerId = session()->get('customer_id');
        model('WishlistModel')->removeFromWishlist($customerId, $productId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        return redirect()->back()->with('success', 'Produto removido dos favoritos.');
    }
}
