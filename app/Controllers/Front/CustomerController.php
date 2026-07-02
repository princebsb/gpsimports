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

    /**
     * LGPD - Exportar dados do cliente
     */
    public function exportData()
    {
        $customerId = session()->get('customer_id');
        $customerModel = model('CustomerModel');
        $customer = $customerModel->find($customerId);

        if (!$customer) {
            return redirect()->back()->with('error', 'Cliente nao encontrado.');
        }

        // Coletar todos os dados do cliente
        $data = [
            'dados_pessoais' => [
                'nome' => $customer['name'],
                'email' => $customer['email'],
                'cpf' => $customer['cpf'],
                'telefone' => $customer['phone'],
                'celular' => $customer['mobile'],
                'data_nascimento' => $customer['birth_date'],
                'genero' => $customer['gender'],
                'criado_em' => $customer['created_at'],
            ],
            'enderecos' => $this->customerService->getAddresses($customerId),
            'pedidos' => model('OrderModel')->getByCustomer($customerId, 1000),
            'favoritos' => model('WishlistModel')->getByCustomer($customerId),
            'newsletter' => model('NewsletterModel')->where('email', $customer['email'])->first(),
            'exportado_em' => date('Y-m-d H:i:s'),
        ];

        // Gerar JSON
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Retornar como download
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="meus_dados_' . date('Y-m-d') . '.json"')
            ->setBody($json);
    }

    /**
     * LGPD - Excluir conta do cliente
     */
    public function deleteAccount()
    {
        $customerId = session()->get('customer_id');
        $customerModel = model('CustomerModel');
        $customer = $customerModel->find($customerId);

        if (!$customer) {
            return redirect()->back()->with('error', 'Cliente nao encontrado.');
        }

        // Validar senha
        $password = $this->request->getPost('password');
        if (!password_verify($password, $customer['password'])) {
            return redirect()->back()->with('error', 'Senha incorreta. A exclusao foi cancelada.');
        }

        // Validar checkbox de confirmacao
        if (!$this->request->getPost('confirm_delete')) {
            return redirect()->back()->with('error', 'Voce deve confirmar a exclusao.');
        }

        // Anonimizar dados do cliente (em vez de deletar completamente para manter historico de pedidos)
        $anonymizedData = [
            'name' => 'Usuario Excluido',
            'email' => 'excluido_' . $customerId . '_' . time() . '@anonimo.local',
            'cpf' => '000.000.000-00',
            'phone' => '',
            'mobile' => '',
            'birth_date' => null,
            'gender' => null,
            'status' => 'deleted',
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $customerModel->update($customerId, $anonymizedData);

        // Excluir enderecos
        model('CustomerAddressModel')->where('customer_id', $customerId)->delete();

        // Excluir favoritos
        model('WishlistModel')->where('customer_id', $customerId)->delete();

        // Remover da newsletter
        model('NewsletterModel')->where('email', $customer['email'])->delete();

        // Deslogar
        session()->destroy();

        return redirect()->to('/')->with('success', 'Sua conta foi excluida com sucesso. Seus dados pessoais foram removidos conforme a LGPD.');
    }
}
