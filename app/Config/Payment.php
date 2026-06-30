<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Payment extends BaseConfig
{
    /**
     * Default payment gateway
     */
    public string $defaultGateway = 'mercadopago';

    /**
     * Available payment gateways
     */
    public array $gateways = [
        'mercadopago' => [
            'name' => 'Mercado Pago',
            'class' => 'App\Libraries\Payment\MercadoPago',
            'enabled' => true,
            'sandbox' => true,
        ],
        'pagseguro' => [
            'name' => 'PagSeguro',
            'class' => 'App\Libraries\Payment\PagSeguro',
            'enabled' => false,
            'sandbox' => true,
        ],
        'asaas' => [
            'name' => 'Asaas',
            'class' => 'App\Libraries\Payment\Asaas',
            'enabled' => false,
            'sandbox' => true,
        ],
    ];

    /**
     * Available payment methods
     */
    public array $methods = [
        'pix' => [
            'name' => 'PIX',
            'enabled' => true,
            'discount' => 5, // 5% discount
            'icon' => 'pix.svg',
        ],
        'credit_card' => [
            'name' => 'Cartao de Credito',
            'enabled' => true,
            'max_installments' => 12,
            'min_installment_value' => 50.00,
            'interest_rate' => 0, // 0% = sem juros
            'icon' => 'credit-card.svg',
        ],
        'debit_card' => [
            'name' => 'Cartao de Debito',
            'enabled' => true,
            'icon' => 'debit-card.svg',
        ],
        'boleto' => [
            'name' => 'Boleto Bancario',
            'enabled' => true,
            'days_to_expire' => 3,
            'discount' => 3, // 3% discount
            'icon' => 'boleto.svg',
        ],
    ];

    /**
     * Mercado Pago Configuration
     */
    public array $mercadopago = [
        'public_key' => '',
        'access_token' => '',
        'sandbox' => true,
        'webhook_secret' => '',
        'statement_descriptor' => 'GPSIMPORTS',
        'notification_url' => '',
    ];

    /**
     * PagSeguro Configuration
     */
    public array $pagseguro = [
        'email' => '',
        'token' => '',
        'sandbox' => true,
    ];

    /**
     * Asaas Configuration
     */
    public array $asaas = [
        'api_key' => '',
        'sandbox' => true,
    ];

    /**
     * Order status mapping
     */
    public array $statusMapping = [
        'pending' => 'Aguardando Pagamento',
        'approved' => 'Pagamento Aprovado',
        'in_process' => 'Em Processamento',
        'rejected' => 'Pagamento Rejeitado',
        'refunded' => 'Reembolsado',
        'cancelled' => 'Cancelado',
        'charged_back' => 'Chargeback',
    ];

    public function __construct()
    {
        parent::__construct();

        // Load Mercado Pago config from .env
        $this->mercadopago['public_key'] = env('mercadopago.publicKey', '');
        $this->mercadopago['access_token'] = env('mercadopago.accessToken', '');
        $this->mercadopago['sandbox'] = env('mercadopago.sandbox', true);
        $this->mercadopago['webhook_secret'] = env('mercadopago.webhookSecret', '');
        $this->mercadopago['notification_url'] = base_url('api/v1/webhooks/mercadopago');
    }
}
