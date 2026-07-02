<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $productService = service('product');
        $bannerModel = model('BannerModel');
        $categoryModel = model('CategoryModel');

        $homeProducts = $productService->getHomeProducts();
        $banners = $bannerModel->getHomeSlider();
        $categories = $categoryModel->getFeatured(8);

        return view('front/home/index', [
            'title' => 'GPS Imports - Sua Loja Online',
            'banners' => $banners,
            'featuredProducts' => $homeProducts['featured'],
            'newProducts' => $homeProducts['new'],
            'bestsellers' => $homeProducts['bestsellers'],
            'onSaleProducts' => $homeProducts['on_sale'],
            'categories' => $categories,
        ]);
    }

    public function subscribeNewsletter()
    {
        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email invalido.']);
        }

        $result = model('NewsletterModel')->subscribe($email, $name);

        return $this->response->setJSON($result);
    }

    public function about()
    {
        return view('front/pages/about', ['title' => 'Sobre Nos']);
    }

    public function contact()
    {
        return view('front/pages/contact', ['title' => 'Contato']);
    }

    public function sendContact()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'subject' => 'required|min_length[5]',
            'message' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->table('contacts')->insert([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Mensagem enviada com sucesso!');
    }

    public function privacy()
    {
        return view('front/pages/privacy', ['title' => 'Politica de Privacidade']);
    }

    public function terms()
    {
        return view('front/pages/terms', ['title' => 'Termos de Uso']);
    }

    public function returns()
    {
        return view('front/pages/returns', ['title' => 'Trocas e Devoluções']);
    }

    public function howToBuy()
    {
        return view('front/pages/how-to-buy', ['title' => 'Como Comprar']);
    }

    public function paymentMethods()
    {
        return view('front/pages/payment-methods', ['title' => 'Formas de Pagamento']);
    }

    public function shipping()
    {
        return view('front/pages/shipping', ['title' => 'Frete e Entrega']);
    }
}
