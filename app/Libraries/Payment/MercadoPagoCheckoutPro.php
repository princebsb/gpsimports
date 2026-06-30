<?php

namespace App\Libraries\Payment;

use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use MercadoPago\Payer;

class MercadoPagoCheckoutPro
{
    protected string $accessToken;
    protected string $publicKey;
    protected bool $sandbox;

    public function __construct()
    {
        $this->accessToken = env('mercadopago.accessToken', '');
        $this->publicKey = env('mercadopago.publicKey', '');
        $this->sandbox = env('mercadopago.sandbox', false) === 'true' || env('mercadopago.sandbox', false) === true;

        // Configurar SDK v2
        SDK::setAccessToken($this->accessToken);
    }

    /**
     * Criar preferencia de pagamento para Checkout Pro
     */
    public function createPreference(array $order, array $items, array $customer): array
    {
        try {
            $preference = new Preference();

            // Preparar itens
            $preferenceItems = [];
            foreach ($items as $item) {
                $mpItem = new Item();
                $mpItem->id = (string) ($item['product_id'] ?? $item['id'] ?? '');
                $mpItem->title = $item['name'];
                $mpItem->quantity = (int) $item['quantity'];
                $mpItem->unit_price = (float) $item['price'];
                $mpItem->currency_id = 'BRL';
                $preferenceItems[] = $mpItem;
            }
            $preference->items = $preferenceItems;

            // Dados do pagador
            $payer = new Payer();
            $nameParts = explode(' ', $customer['name']);
            $payer->name = $nameParts[0];
            $payer->surname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : $nameParts[0];
            $payer->email = $customer['email'];

            $cpf = preg_replace('/\D/', '', $customer['cpf'] ?? '');
            if ($cpf) {
                $payer->identification = [
                    'type' => 'CPF',
                    'number' => $cpf,
                ];
            }

            if (!empty($customer['phone'])) {
                $phone = preg_replace('/\D/', '', $customer['phone']);
                $payer->phone = [
                    'area_code' => substr($phone, 0, 2),
                    'number' => substr($phone, 2),
                ];
            }

            $preference->payer = $payer;

            // URLs de retorno
            $baseUrl = base_url();
            $preference->back_urls = [
                'success' => $baseUrl . 'checkout/sucesso/' . $order['order_number'],
                'failure' => $baseUrl . 'checkout/falha/' . $order['order_number'],
                'pending' => $baseUrl . 'checkout/pendente/' . $order['order_number'],
            ];
            $preference->auto_return = 'approved';

            // Referencia externa (numero do pedido)
            $preference->external_reference = $order['order_number'];

            // URL de notificacao (webhook)
            $preference->notification_url = $baseUrl . 'webhook/mercadopago';

            // Descricao no extrato
            $preference->statement_descriptor = 'GPS IMPORTS';

            // Configuracao de pagamento
            $preference->payment_methods = [
                'installments' => 6, // Maximo 6 parcelas
                'default_installments' => 1,
            ];

            // Expiracao
            $preference->expires = true;
            $preference->expiration_date_from = date('c');
            $preference->expiration_date_to = date('c', strtotime('+2 days'));

            // Salvar preferencia
            $preference->save();

            if ($preference->id) {
                return [
                    'success' => true,
                    'preference_id' => $preference->id,
                    'init_point' => $this->sandbox ? $preference->sandbox_init_point : $preference->init_point,
                    'sandbox_init_point' => $preference->sandbox_init_point,
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao criar preferencia de pagamento.',
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
