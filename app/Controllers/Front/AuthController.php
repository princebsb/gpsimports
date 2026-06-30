<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    protected $customerService;

    public function __construct()
    {
        $this->customerService = service('customer');
    }

    public function login()
    {
        if (session()->get('customer_logged_in')) {
            return redirect()->to('/minha-conta');
        }

        return view('front/auth/login', [
            'title' => 'Entrar',
            'redirect_url' => $this->request->getGet('redirect') ?? session()->getFlashdata('redirect_url') ?? '/minha-conta',
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

        $result = $this->customerService->authenticate(
            $this->request->getPost('email'),
            $this->request->getPost('password')
        );

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        $redirectUrl = $this->request->getPost('redirect_url') ?: '/minha-conta';
        return redirect()->to($redirectUrl)->with('success', $result['message']);
    }

    public function register()
    {
        if (session()->get('customer_logged_in')) {
            return redirect()->to('/minha-conta');
        }

        return view('front/auth/register', [
            'title' => 'Criar Conta',
            'redirect_url' => $this->request->getGet('redirect') ?? session()->getFlashdata('redirect_url') ?? '/minha-conta',
        ]);
    }

    public function attemptRegister()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|is_unique[customers.email]',
            'cpf' => 'required|min_length[11]|max_length[14]',
            'phone' => 'required|min_length[10]|max_length[15]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'terms' => 'required',
        ];

        $messages = [
            'first_name' => [
                'required' => 'Informe seu nome.',
                'min_length' => 'Nome deve ter pelo menos 2 caracteres.',
            ],
            'last_name' => [
                'required' => 'Informe seu sobrenome.',
                'min_length' => 'Sobrenome deve ter pelo menos 2 caracteres.',
            ],
            'email' => [
                'required' => 'Informe seu e-mail.',
                'valid_email' => 'E-mail invalido.',
                'is_unique' => 'Este e-mail ja esta cadastrado.',
            ],
            'cpf' => [
                'required' => 'Informe seu CPF.',
                'min_length' => 'CPF invalido.',
            ],
            'phone' => [
                'required' => 'Informe seu telefone.',
                'min_length' => 'Telefone invalido.',
            ],
            'password' => [
                'required' => 'Informe uma senha.',
                'min_length' => 'Senha deve ter pelo menos 8 caracteres.',
            ],
            'password_confirm' => [
                'required' => 'Confirme sua senha.',
                'matches' => 'As senhas nao conferem.',
            ],
            'terms' => [
                'required' => 'Voce deve aceitar os termos de uso.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validar CPF
        $cpf = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
        if (!$this->validateCPF($cpf)) {
            return redirect()->back()->withInput()->with('error', 'CPF invalido.');
        }

        // Validar telefone
        $phone = preg_replace('/[^0-9]/', '', $this->request->getPost('phone'));
        if (!$this->validatePhone($phone)) {
            return redirect()->back()->withInput()->with('error', 'Telefone invalido.');
        }

        // Preparar dados
        $data = $this->request->getPost();
        $data['name'] = trim($data['first_name'] . ' ' . $data['last_name']);
        $data['cpf'] = $cpf;
        $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);

        $result = $this->customerService->register($data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        // Redirecionar para checkout se veio de la
        $redirectUrl = $this->request->getPost('redirect_url');
        if ($redirectUrl && strpos($redirectUrl, 'checkout') !== false) {
            return redirect()->to($redirectUrl)->with('success', 'Conta criada! Continue sua compra.');
        }

        return redirect()->to('/minha-conta')->with('success', $result['message']);
    }

    /**
     * Validar CPF
     */
    protected function validateCPF(string $cpf): bool
    {
        // Remove caracteres nao numericos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se tem 11 digitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os digitos sao iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Calcula primeiro digito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += (int) $cpf[$i] * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;

        if ((int) $cpf[9] != $digito1) {
            return false;
        }

        // Calcula segundo digito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += (int) $cpf[$i] * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        if ((int) $cpf[10] != $digito2) {
            return false;
        }

        return true;
    }

    /**
     * Validar telefone brasileiro
     */
    protected function validatePhone(string $phone): bool
    {
        // Remove caracteres nao numericos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Deve ter 10 (fixo) ou 11 (celular) digitos
        $length = strlen($phone);
        if ($length < 10 || $length > 11) {
            return false;
        }

        // DDD valido (11-99)
        $ddd = (int) substr($phone, 0, 2);
        if ($ddd < 11 || $ddd > 99) {
            return false;
        }

        // Se celular (11 digitos), deve comecar com 9
        if ($length === 11 && $phone[2] !== '9') {
            return false;
        }

        return true;
    }

    public function logout()
    {
        $this->customerService->logout();
        return redirect()->to('/')->with('success', 'Voce saiu da sua conta.');
    }

    public function forgotPassword()
    {
        return view('front/auth/forgot-password', [
            'title' => 'Esqueci Minha Senha',
        ]);
    }

    public function sendResetLink()
    {
        $rules = ['email' => 'required|valid_email'];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->customerService->requestPasswordReset($this->request->getPost('email'));

        return redirect()->back()->with('success', $result['message']);
    }

    public function resetPassword($token)
    {
        $resetModel = model('PasswordResetModel');
        $reset = $resetModel->validateToken($token, 'customer');

        if (!$reset) {
            return redirect()->to('/login')->with('error', 'Link invalido ou expirado.');
        }

        return view('front/auth/reset-password', [
            'title' => 'Redefinir Senha',
            'token' => $token,
        ]);
    }

    public function attemptReset()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $result = $this->customerService->resetPassword(
            $this->request->getPost('token'),
            $this->request->getPost('password')
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to('/login')->with('success', $result['message']);
    }
}
