<?php

namespace App\Libraries\Payment;

class MercadoPagoCheckoutPro
{
    protected string $accessToken;
    protected string $publicKey;
    protected bool $sandbox;
    protected string $apiUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $this->accessToken = env('mercadopago.accessToken', '');
        $this->publicKey = env('mercadopago.publicKey', '');
        $this->sandbox = env('mercadopago.sandbox', false) === 'true' || env('mercadopago.sandbox', false) === true;
    }

    /**
     * Criar preferencia de pagamento para Checkout Pro (via API REST)
     */
    public function createPreference(array $order, array $items, array $customer): array
    {
        try {
            // Preparar itens
            $preferenceItems = [];
            foreach ($items as $item) {
                $preferenceItems[] = [
                    'id' => (string) ($item['product_id'] ?? $item['id'] ?? ''),
                    'title' => $item['name'],
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => (float) $item['price'],
                    'currency_id' => 'BRL',
                ];
            }

            // Adicionar frete como item separado se houver custo de envio
            $shippingCost = (float) ($order['shipping_cost'] ?? 0);
            if ($shippingCost > 0) {
                $preferenceItems[] = [
                    'id' => 'FRETE',
                    'title' => 'Frete - ' . ($order['shipping_method'] ?? 'Envio'),
                    'quantity' => 1,
                    'unit_price' => $shippingCost,
                    'currency_id' => 'BRL',
                ];
            }

            // Dados do pagador
            $nameParts = explode(' ', $customer['name']);
            $cpf = preg_replace('/\D/', '', $customer['cpf'] ?? '');
            $phone = preg_replace('/\D/', '', $customer['phone'] ?? '');

            $payer = [
                'name' => $nameParts[0],
                'surname' => count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : $nameParts[0],
                'email' => $customer['email'],
            ];

            if ($cpf) {
                $payer['identification'] = [
                    'type' => 'CPF',
                    'number' => $cpf,
                ];
            }

            if ($phone) {
                $payer['phone'] = [
                    'area_code' => substr($phone, 0, 2),
                    'number' => substr($phone, 2),
                ];
            }

            // URLs de retorno
            $baseUrl = base_url();

            // Montar payload da preferencia
            $preferenceData = [
                'items' => $preferenceItems,
                'payer' => $payer,
                'back_urls' => [
                    'success' => $baseUrl . 'checkout/sucesso/' . $order['order_number'],
                    'failure' => $baseUrl . 'checkout/falha/' . $order['order_number'],
                    'pending' => $baseUrl . 'checkout/pendente/' . $order['order_number'],
                ],
                'auto_return' => 'approved',
                'external_reference' => $order['order_number'],
                'notification_url' => $baseUrl . 'webhook/mercadopago',
                'statement_descriptor' => 'GPS IMPORTS',
                'payment_methods' => [
                    'installments' => 12,
                    'default_installments' => 1,
                ],
                'expires' => true,
                'expiration_date_from' => date('c'),
                'expiration_date_to' => date('c', strtotime('+2 days')),
            ];

            // Log para debug
            log_message('debug', 'MercadoPago Preference Request: ' . json_encode($preferenceData));

            // Fazer requisicao para API
            $response = $this->request('POST', '/checkout/preferences', $preferenceData);

            // Log da resposta
            log_message('debug', 'MercadoPago Preference Response: ' . json_encode($response));

            if (!empty($response['id'])) {
                return [
                    'success' => true,
                    'preference_id' => $response['id'],
                    'init_point' => $this->sandbox ? $response['sandbox_init_point'] : $response['init_point'],
                    'sandbox_init_point' => $response['sandbox_init_point'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Erro ao criar preferencia de pagamento.',
            ];

        } catch (\Exception $e) {
            log_message('error', 'MercadoPago createPreference Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Fazer requisicao para API do Mercado Pago
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
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
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'MercadoPago cURL Error: ' . $error);
            throw new \Exception('Erro de conexao: ' . $error);
        }

        $decoded = json_decode($response, true) ?? [];

        if ($httpCode >= 400) {
            log_message('error', 'MercadoPago API Error HTTP ' . $httpCode . ': ' . $response);
        }

        return $decoded;
    }

    /**
     * Consultar pagamento por ID
     */
    public function getPayment(string $paymentId): ?array
    {
        try {
            $payment = \MercadoPago\Payment::find_by_id($paymentId);

            if (!$payment || !$payment->id) {
                return null;
            }

            return [
                'id' => $payment->id,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'payment_method_id' => $payment->payment_method_id,
                'payment_type_id' => $payment->payment_type_id,
                'installments' => $payment->installments,
                'transaction_amount' => $payment->transaction_amount,
                'external_reference' => $payment->external_reference,
                'date_approved' => $payment->date_approved,
                'date_created' => $payment->date_created,
                'payer_email' => $payment->payer->email ?? null,
            ];

        } catch (\Exception $e) {
            log_message('error', 'MercadoPago getPayment Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Processar notificacao do webhook
     */
    public function processWebhook(array $data): ?array
    {
        // Webhook do Mercado Pago pode vir em diferentes formatos
        $paymentId = null;

        if (isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];
        } elseif (isset($data['id']) && ($data['type'] ?? '') === 'payment') {
            $paymentId = $data['id'];
        }

        if (!$paymentId) {
            log_message('warning', 'MercadoPago Webhook: Payment ID not found in data: ' . json_encode($data));
            return null;
        }

        // Buscar detalhes do pagamento
        $payment = $this->getPayment($paymentId);

        if (!$payment) {
            return null;
        }

        return [
            'payment_id' => $payment['id'],
            'external_reference' => $payment['external_reference'],
            'status' => $this->mapStatus($payment['status']),
            'status_detail' => $payment['status_detail'],
            'payment_method' => $payment['payment_method_id'],
            'payment_type' => $payment['payment_type_id'],
            'installments' => $payment['installments'],
            'amount' => $payment['transaction_amount'],
            'date_approved' => $payment['date_approved'],
        ];
    }

    /**
     * Mapear status do Mercado Pago para status interno
     */
    protected function mapStatus(string $status): string
    {
        return match ($status) {
            'approved' => 'approved',
            'pending', 'in_process', 'authorized' => 'pending',
            'rejected', 'cancelled', 'refunded', 'charged_back' => 'rejected',
            default => 'pending',
        };
    }

    /**
     * Reembolsar pagamento
     */
    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            $payment = \MercadoPago\Payment::find_by_id($paymentId);

            if (!$payment) {
                return [
                    'success' => false,
                    'message' => 'Pagamento nao encontrado.',
                ];
            }

            if ($amount !== null) {
                $payment->refund($amount);
            } else {
                $payment->refund();
            }

            return [
                'success' => true,
                'refund_id' => $payment->id,
                'status' => 'refunded',
            ];

        } catch (\Exception $e) {
            log_message('error', 'MercadoPago refund Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar reembolso.',
            ];
        }
    }

    /**
     * Retorna a Public Key
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
