<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SettingsController extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = model('SettingModel');
    }

    public function index()
    {
        $settings = $this->settingModel->getAll();

        return view('admin/settings/index', [
            'title' => 'Configuracoes',
            'settings' => $settings,
        ]);
    }

    public function save()
    {
        $data = $this->request->getPost();

        foreach ($data as $key => $value) {
            if (strpos($key, 'setting_') === 0) {
                $settingKey = substr($key, 8);
                $this->settingModel->setValue($settingKey, $value);
            }
        }

        // Handle logo upload
        $logo = $this->request->getFile('site_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = 'logo_' . time() . '.' . $logo->getExtension();
            $logo->move(FCPATH . 'uploads/', $newName);
            $this->settingModel->setValue('site_logo', $newName, 'general', 'image');
        }

        // Handle favicon upload
        $favicon = $this->request->getFile('site_favicon');
        if ($favicon && $favicon->isValid() && !$favicon->hasMoved()) {
            $newName = 'favicon_' . time() . '.' . $favicon->getExtension();
            $favicon->move(FCPATH . 'uploads/', $newName);
            $this->settingModel->setValue('site_favicon', $newName, 'general', 'image');
        }

        return redirect()->back()->with('success', 'Configuracoes salvas com sucesso!');
    }

    public function store()
    {
        $settings = $this->settingModel->getByGroup('store');

        return view('admin/settings/store', [
            'title' => 'Configuracoes da Loja',
            'settings' => $settings,
        ]);
    }

    public function saveStore()
    {
        $data = $this->request->getPost();

        $storeSettings = [
            'store_name', 'store_email', 'store_phone', 'store_whatsapp',
            'store_address', 'store_cnpj', 'store_city', 'store_state',
            'store_zipcode', 'store_hours',
        ];

        foreach ($storeSettings as $key) {
            if (isset($data[$key])) {
                $this->settingModel->setValue($key, $data[$key], 'store');
            }
        }

        return redirect()->back()->with('success', 'Configuracoes da loja salvas!');
    }

    public function payment()
    {
        $settings = $this->settingModel->getByGroup('payment');

        return view('admin/settings/payment', [
            'title' => 'Configuracoes de Pagamento',
            'settings' => $settings,
        ]);
    }

    public function savePayment()
    {
        $data = $this->request->getPost();

        $paymentSettings = [
            'mp_public_key', 'mp_access_token', 'mp_sandbox',
            'pix_enabled', 'pix_discount',
            'credit_card_enabled', 'credit_card_max_installments', 'credit_card_min_installment',
            'boleto_enabled', 'boleto_discount', 'boleto_days',
        ];

        foreach ($paymentSettings as $key) {
            if (isset($data[$key])) {
                $this->settingModel->setValue($key, $data[$key], 'payment');
            }
        }

        return redirect()->back()->with('success', 'Configuracoes de pagamento salvas!');
    }

    public function shipping()
    {
        $settings = $this->settingModel->getByGroup('shipping');

        return view('admin/settings/shipping', [
            'title' => 'Configuracoes de Frete',
            'settings' => $settings,
        ]);
    }

    public function saveShipping()
    {
        $data = $this->request->getPost();

        $shippingSettings = [
            'shipping_origin_zipcode', 'shipping_handling_days',
            'free_shipping_enabled', 'free_shipping_min_value',
            'correios_enabled', 'correios_pac_enabled', 'correios_sedex_enabled',
        ];

        foreach ($shippingSettings as $key) {
            if (isset($data[$key])) {
                $this->settingModel->setValue($key, $data[$key], 'shipping');
            }
        }

        return redirect()->back()->with('success', 'Configuracoes de frete salvas!');
    }

    public function email()
    {
        $settings = $this->settingModel->getByGroup('email');

        return view('admin/settings/email', [
            'title' => 'Configuracoes de Email',
            'settings' => $settings,
        ]);
    }

    public function saveEmail()
    {
        $data = $this->request->getPost();

        $emailSettings = [
            'email_from_name', 'email_from_address',
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_crypto',
        ];

        foreach ($emailSettings as $key) {
            if (isset($data[$key])) {
                $this->settingModel->setValue($key, $data[$key], 'email');
            }
        }

        return redirect()->back()->with('success', 'Configuracoes de email salvas!');
    }

    public function testEmail()
    {
        $email = \Config\Services::email();

        $settings = $this->settingModel->getByGroup('email');

        $email->setFrom($settings['email_from_address'] ?? 'test@test.com', $settings['email_from_name'] ?? 'Test');
        $email->setTo($this->request->getPost('test_email'));
        $email->setSubject('Teste de Email - GPS Imports');
        $email->setMessage('Este e um email de teste do sistema GPS Imports.');

        if ($email->send()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Email enviado com sucesso!']);
        }

        return $this->response->setJSON(['success' => false, 'message' => $email->printDebugger(['headers'])]);
    }
}
