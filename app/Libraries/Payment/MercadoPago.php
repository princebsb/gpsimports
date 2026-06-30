<?php

namespace App\Libraries\Payment;

use Config\Payment;

class MercadoPago implements PaymentInterface
{
    protected Payment $config;
    protected string $apiUrl;
    protected string $accessToken;

    public function __construct()
    {
        $this->config = config('Payment');
        $this->accessToken = $this->config->mercadopago['access_token'];
        $this->apiUrl = 'https://api.mercadopago.com';
    }

    public function createPixPayment(array $order): array
    {
        // Modo de simulacao para testes locais
        if ($this->isTestMode()) {
            return $this->simulatePixPayment($order);
        }

        $customer = model('CustomerModel')->find($order['customer_id']);

        $payload = [
            'transaction_amount' => (float) $order['total'],
            'description' => 'Pedido #' . $order['order_number'],
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $customer['email'],
                'first_name' => explode(' ', $customer['name'])[0],
                'last_name' => implode(' ', array_slice(explode(' ', $customer['name']), 1)) ?: $customer['name'],
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $customer['cpf'] ?? '00000000000'),
                ],
            ],
            'external_reference' => $order['order_number'],
            'notification_url' => $this->config->mercadopago['notification_url'],
        ];

        $response = $this->request('POST', '/v1/payments', $payload);

        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];

        return [
            'success' => true,
            'transaction_id' => $data['id'],
            'external_id' => $data['id'],
            'status' => $this->mapStatus($data['status']),
            'qr_code' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
            'qr_code_base64' => $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
            'copy_paste' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
            'expiration' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
            'raw_response' => $data,
        ];
    }

    /**
     * Verifica se esta em modo de teste (sem credenciais validas)
     */
    protected function isTestMode(): bool
    {
        return empty($this->accessToken) || strpos($this->accessToken, 'xxxx') !== false;
    }

    /**
     * Simula pagamento PIX para testes locais
     */
    protected function simulatePixPayment(array $order): array
    {
        $transactionId = 'SIM_PIX_' . time() . '_' . rand(1000, 9999);

        // QR Code de exemplo (texto simples para testes)
        $pixCode = '00020126580014br.gov.bcb.pix0136' . $transactionId . '5204000053039865802BR5925GPS IMPORTS6009SAO PAULO62070503***6304' . strtoupper(substr(md5($transactionId), 0, 4));

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'external_id' => $transactionId,
            'status' => 'pending',
            'qr_code' => $pixCode,
            'qr_code_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'copy_paste' => $pixCode,
            'expiration' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
            'raw_response' => ['simulated' => true],
        ];
    }

    /**
     * Simula pagamento Boleto para testes locais
     */
    protected function simulateBoletoPayment(array $order): array
    {
        $transactionId = 'SIM_BOL_' . time() . '_' . rand(1000, 9999);
        $barcode = '23793.38128 60000.000003 00000.000400 1 ' . date('ymd') . '0000' . str_pad((int)($order['total'] * 100), 10, '0', STR_PAD_LEFT);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'external_id' => $transactionId,
            'status' => 'pending',
            'boleto_url' => base_url('uploads/boletos/simulado.pdf'),
            'barcode' => $barcode,
            'expiration' => date('Y-m-d', strtotime('+3 days')),
            'raw_response' => ['simulated' => true],
        ];
    }

    /**
     * Simula pagamento com Cartao para testes locais
     */
    protected function simulateCardPayment(array $order, array $cardData, string $type): array
    {
        $transactionId = 'SIM_CARD_' . time() . '_' . rand(1000, 9999);

        // Simular aprovacao (para testes)
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'external_id' => $transactionId,
            'status' => 'approved',
            'card_brand' => 'visa',
            'card_last_digits' => substr($cardData['card_number'] ?? '0000', -4),
            'raw_response' => ['simulated' => true],
        ];
    }

    public function createCardPayment(array $order, array $cardData, string $type = 'credit'): array
    {
        // Modo de simulacao para testes locais
        if ($this->isTestMode()) {
            return $this->simulateCardPayment($order, $cardData, $type);
        }

        $customer = model('CustomerModel')->find($order['customer_id']);

        $payload = [
            'transaction_amount' => (float) $order['total'],
            'description' => 'Pedido #' . $order['order_number'],
            'payment_method_id' => $cardData['payment_method_id'] ?? 'visa',
            'token' => $cardData['token'],
            'installments' => (int) ($cardData['installments'] ?? 1),
            'payer' => [
                'email' => $customer['email'],
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $cardData['cpf'] ?? $customer['cpf'] ?? ''),
                ],
            ],
            'external_reference' => $order['order_number'],
            'statement_descriptor' => $this->config->mercadopago['statement_descriptor'],
            'notification_url' => $this->config->mercadopago['notification_url'],
            'capture' => true,
        ];

        if ($type === 'debit') {
            $payload['payment_type_id'] = 'debit_card';
        }

        $response = $this->request('POST', '/v1/payments', $payload);

        if (!$response['success']) {
            return [
                'success' => false,
                'message' => $this->getErrorMessage($response['data']['cause'] ?? []),
                'raw_response' => $response['data'],
            ];
        }

        $data = $response['data'];

        return [
            'success' => true,
            'transaction_id' => $data['id'],
            'external_id' => $data['id'],
            'status' => $this->mapStatus($data['status']),
            'card_brand' => $data['payment_method_id'] ?? null,
            'card_last_digits' => $data['card']['last_four_digits'] ?? null,
            'raw_response' => $data,
        ];
    }

    public function createBoletoPayment(array $order): array
    {
        // Modo de simulacao para testes locais
        if ($this->isTestMode()) {
            return $this->simulateBoletoPayment($order);
        }

        $customer = model('CustomerModel')->find($order['customer_id']);

        $payload = [
            'transaction_amount' => (float) $order['total'],
            'description' => 'Pedido #' . $order['order_number'],
            'payment_method_id' => 'bolbradesco',
            'payer' => [
                'email' => $customer['email'],
                'first_name' => explode(' ', $customer['name'])[0],
                'last_name' => implode(' ', array_slice(explode(' ', $customer['name']), 1)) ?: $customer['name'],
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $customer['cpf'] ?? ''),
                ],
            ],
            'external_reference' => $order['order_number'],
            'notification_url' => $this->config->mercadopago['notification_url'],
        ];

        $response = $this->request('POST', '/v1/payments', $payload);

        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];

        return [
            'success' => true,
            'transaction_id' => $data['id'],
            'external_id' => $data['id'],
            'status' => $this->mapStatus($data['status']),
            'boleto_url' => $data['transaction_details']['external_resource_url'] ?? null,
            'barcode' => $data['barcode']['content'] ?? null,
            'expiration' => date('Y-m-d', strtotime('+3 days')),
            'raw_response' => $data,
        ];
    }

    public function parseWebhook(array $data): ?array
    {
        $paymentId = $data['data']['id'] ?? $data['id'] ?? null;
        if (!$paymentId) return null;

        $response = $this->request('GET', "/v1/payments/{$paymentId}");
        if (!$response['success']) return null;

        $payment = $response['data'];

        return [
            'transaction_id' => $payment['id'],
            'external_id' => $payment['id'],
            'status' => $this->mapStatus($payment['status']),
            'external_reference' => $payment['external_reference'] ?? null,
        ];
    }

    public function getPaymentStatus(string $paymentId): array
    {
        $response = $this->request('GET', "/v1/payments/{$paymentId}");

        if (!$response['success']) {
            return ['success' => false, 'message' => 'Erro ao consultar pagamento.'];
        }

        return [
            'success' => true,
            'status' => $this->mapStatus($response['data']['status']),
            'data' => $response['data'],
        ];
    }

    public function refund(string $paymentId, float $amount): array
    {
        $response = $this->request('POST', "/v1/payments/{$paymentId}/refunds", ['amount' => $amount]);

        if (!$response['success']) {
            return ['success' => false, 'message' => 'Erro ao processar reembolso.'];
        }

        return [
            'success' => true,
            'refund_id' => $response['data']['id'],
            'status' => $response['data']['status'],
        ];
    }

    protected function request(string $method, string $endpoint, array $data = []): array
    {
        // Verificar se as credenciais estão configuradas
        if (empty($this->accessToken) || strpos($this->accessToken, 'xxxx') !== false) {
            log_message('error', 'MercadoPago: Access token nao configurado ou invalido');
            return ['success' => false, 'message' => 'Gateway de pagamento nao configurado. Entre em contato com o suporte.', 'data' => []];
        }

        $ch = curl_init();
        $url = $this->apiUrl . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
            'X-Idempotency-Key: ' . uniqid('mp_', true),
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError) {
            log_message('error', 'MercadoPago CURL Error: ' . $curlError);
            return ['success' => false, 'message' => 'Erro de conexao com o gateway.', 'data' => []];
        }

        $responseData = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMsg = $responseData['message'] ?? 'Erro no gateway de pagamento.';
            log_message('error', 'MercadoPago Error: HTTP ' . $httpCode . ' - ' . json_encode($responseData));
            return ['success' => false, 'message' => $errorMsg, 'data' => $responseData];
        }

        return ['success' => true, 'data' => $responseData];
    }

    protected function mapStatus(string $status): string
    {
        return match ($status) {
            'approved' => 'approved',
            'pending', 'in_process' => 'pending',
            'rejected', 'cancelled' => 'rejected',
            'refunded' => 'refunded',
            default => 'pending',
        };
    }

    protected function getErrorMessage(array $causes): string
    {
        $messages = [
            'cc_rejected_insufficient_amount' => 'Saldo insuficiente.',
            'cc_rejected_bad_filled_card_number' => 'Numero do cartao invalido.',
            'cc_rejected_high_risk' => 'Pagamento recusado por seguranca.',
        ];

        foreach ($causes as $cause) {
            if (isset($messages[$cause['code'] ?? ''])) {
                return $messages[$cause['code']];
            }
        }

        return 'Pagamento recusado.';
    }

    public function getPublicKey(): string
    {
        return $this->config->mercadopago['public_key'];
    }
}
