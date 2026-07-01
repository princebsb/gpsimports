<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MelhorEnvioController extends BaseController
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected string $baseUrl;
    protected bool $sandbox;

    public function __construct()
    {
        $this->clientId = env('melhorenvio.clientId', '26610');
        $this->clientSecret = env('melhorenvio.clientSecret', 'mffMk9gH5RVIge0qwJHTGxtIUThS5qZbothfu4yK');
        $this->redirectUri = base_url('melhor-envio/callback');
        $this->sandbox = env('melhorenvio.sandbox', false);
        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.melhorenvio.com.br'
            : 'https://melhorenvio.com.br';
    }

    /**
     * Redireciona para autorizacao do Melhor Envio
     */
    public function authorize()
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'cart-read cart-write companies-read companies-write coupons-read coupons-write notifications-read orders-read products-read products-write purchases-read shipping-calculate shipping-cancel shipping-checkout shipping-companies shipping-generate shipping-preview shipping-print shipping-share shipping-tracking ecommerce-shipping transactions-read users-read users-write',
            'state' => bin2hex(random_bytes(16)),
        ]);

        return redirect()->to($this->baseUrl . '/oauth/authorize?' . $params);
    }

    /**
     * Callback do OAuth - recebe o codigo de autorizacao
     */
    public function callback()
    {
        $code = $this->request->getGet('code');
        $error = $this->request->getGet('error');

        if ($error) {
            return redirect()->to('/admin/configuracoes/frete')
                ->with('error', 'Autorizacao negada: ' . ($this->request->getGet('error_description') ?? $error));
        }

        if (!$code) {
            return redirect()->to('/admin/configuracoes/frete')
                ->with('error', 'Codigo de autorizacao nao recebido.');
        }

        // Trocar codigo por token
        $tokenData = $this->exchangeCodeForToken($code);

        if (!$tokenData || empty($tokenData['access_token'])) {
            return redirect()->to('/admin/configuracoes/frete')
                ->with('error', 'Erro ao obter token de acesso.');
        }

        // Salvar tokens no banco de dados
        $this->saveTokens($tokenData);

        return redirect()->to('/admin/configuracoes/frete')
            ->with('success', 'Melhor Envio conectado com sucesso! Token valido por ' . ($tokenData['expires_in'] / 86400) . ' dias.');
    }

    /**
     * Troca o codigo de autorizacao por um token de acesso
     */
    protected function exchangeCodeForToken(string $code): ?array
    {
        $ch = curl_init();

        $postData = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            log_message('error', 'MelhorEnvio Token Error: HTTP ' . $httpCode . ' - ' . $response);
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * Salva os tokens no banco de dados (tabela settings)
     */
    protected function saveTokens(array $tokenData): void
    {
        $settingModel = model('SettingModel');

        $settingModel->set('melhorenvio_access_token', $tokenData['access_token']);
        $settingModel->set('melhorenvio_refresh_token', $tokenData['refresh_token'] ?? '');
        $settingModel->set('melhorenvio_token_expires', date('Y-m-d H:i:s', time() + ($tokenData['expires_in'] ?? 2592000)));

        // Atualizar o .env com o token (opcional - para compatibilidade)
        log_message('info', 'MelhorEnvio tokens salvos com sucesso.');
    }

    /**
     * Refresh do token quando expirar
     */
    public function refreshToken(): bool
    {
        $settingModel = model('SettingModel');
        $refreshToken = $settingModel->get('melhorenvio_refresh_token');

        if (empty($refreshToken)) {
            return false;
        }

        $ch = curl_init();

        $postData = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . '/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            log_message('error', 'MelhorEnvio Refresh Token Error: ' . $response);
            return false;
        }

        $tokenData = json_decode($response, true);

        if (!empty($tokenData['access_token'])) {
            $this->saveTokens($tokenData);
            return true;
        }

        return false;
    }

    /**
     * Desconectar Melhor Envio
     */
    public function disconnect()
    {
        $settingModel = model('SettingModel');

        $settingModel->set('melhorenvio_access_token', '');
        $settingModel->set('melhorenvio_refresh_token', '');
        $settingModel->set('melhorenvio_token_expires', '');

        return redirect()->to('/admin/configuracoes/frete')
            ->with('success', 'Melhor Envio desconectado.');
    }
}
