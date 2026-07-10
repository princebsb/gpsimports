<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Shipping extends BaseConfig
{
    /**
     * Default shipping provider
     */
    public string $defaultProvider = 'melhorenvio';

    /**
     * Origin ZIP code
     */
    public string $originZipCode = '87140000';

    /**
     * Available shipping providers
     */
    public array $providers = [
        'correios' => [
            'name' => 'Correios',
            'class' => 'App\Libraries\Shipping\Correios',
            'enabled' => true,
        ],
        'melhorenvio' => [
            'name' => 'Melhor Envio',
            'class' => 'App\Libraries\Shipping\MelhorEnvio',
            'enabled' => true,
        ],
        'jadlog' => [
            'name' => 'Jadlog',
            'class' => 'App\Libraries\Shipping\Jadlog',
            'enabled' => false,
        ],
    ];

    /**
     * Correios service codes
     */
    public array $correiosServices = [
        '04014' => [
            'name' => 'SEDEX',
            'code' => '04014',
            'enabled' => true,
        ],
        '04510' => [
            'name' => 'PAC',
            'code' => '04510',
            'enabled' => true,
        ],
        '04782' => [
            'name' => 'SEDEX 12',
            'code' => '04782',
            'enabled' => false,
        ],
        '04790' => [
            'name' => 'SEDEX 10',
            'code' => '04790',
            'enabled' => false,
        ],
    ];

    /**
     * Correios API credentials
     */
    public array $correios = [
        'usuario' => '',
        'senha' => '',
        'codigo_empresa' => '',
        'contrato' => '',
        'cartao_postagem' => '',
    ];

    /**
     * Melhor Envio API credentials
     */
    public array $melhorenvio = [
        'client_id' => '24511',
        'client_secret' => 'TG3L7n1NV0BwUqCKhSEs9sncbnmOL1dopEntZiHH',
        'redirect_uri' => 'https://instrutorlegal.org/api/loja/melhor-envio/callback',
        'user_agent' => 'Instrutor Legal (contato@instrutorlegal.org)',
        'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyNDUxMSIsImp0aSI6IjM0ZjQzYjdiYzg2MzhkODk0OGE1YTZlOTA2ZWEyNGMyZGVlMjY2NmE1OGVjYWEwNzYwODA5NGZkNjI0NTEwZTUwYjI5ODhmMTNkY2JlNmY5IiwiaWF0IjoxNzgwMzE2NzYxLjQwNDU2MywibmJmIjoxNzgwMzE2NzYxLjQwNDU2NSwiZXhwIjoxNzgyOTA4NzYxLjM4MjM0LCJzdWIiOiJiMjE1MDRjZi1mOWFiLTQyZmYtYjY0YS0wMzU4NjI1OTBjMzkiLCJzY29wZXMiOlsiY2FydC1yZWFkIiwiY2FydC13cml0ZSIsImNvbXBhbmllcy1yZWFkIiwiY29tcGFuaWVzLXdyaXRlIiwiY291cG9ucy1yZWFkIiwiY291cG9ucy13cml0ZSIsIm5vdGlmaWNhdGlvbnMtcmVhZCIsIm9yZGVycy1yZWFkIiwicHJvZHVjdHMtcmVhZCIsInByb2R1Y3RzLXdyaXRlIiwicHVyY2hhc2VzLXJlYWQiLCJzaGlwcGluZy1jYWxjdWxhdGUiLCJzaGlwcGluZy1jYW5jZWwiLCJzaGlwcGluZy1jaGVja291dCIsInNoaXBwaW5nLWNvbXBhbmllcyIsInNoaXBwaW5nLWdlbmVyYXRlIiwic2hpcHBpbmctcHJldmlldyIsInNoaXBwaW5nLXByaW50Iiwic2hpcHBpbmctc2hhcmUiLCJzaGlwcGluZy10cmFja2luZyIsImVjb21tZXJjZS1zaGlwcGluZyIsInRyYW5zYWN0aW9ucy1yZWFkIiwidXNlcnMtcmVhZCIsInVzZXJzLXdyaXRlIiwid2ViaG9va3MtcmVhZCIsIndlYmhvb2tzLXdyaXRlIl19.IGNsyURueeTlwCaGXIYy8MBj3VWweyfnE2vzJGt39cRVUskXjGh0C0iMol1DwYY-5wbV1TOFMqqfMtE_N_oAk7kF0zsIcgB9S7HAQZeST5XVt5YuiPoQ3aeWK8k-1520SHiv5HvuAm_l6jReRm6r_wYlS9Lz892-zaGrqOUeJyxgCjY6vGD5_DGloMWAvAyA3b88x9drNR4MgzS9h_XixSV5Z8ActygKMAvyoh5w1aCu4n21vaaP6hPW9lNM6_mjv6yfvYAt-r93z0k4DVPaahVKMwvtHqZIVvWMQjpQ4i1H0Nyxv1OSxDZLRzMJcbwYvqPJD0pEv1QhgosniJp7DllQrnIJs8qggT9TXuE7u56JWhHPQ-8pSrDH1yEgBiyK7W-ipeQvoHpZ941bYy6crjUE7be_9BGKhlv89dUvMeSmA0qXWaB1BRMP7nJrazs0gb2tbsqObHByR2_seb_mA6mbjD6RDGDpse3mViQPOyZ4sT3WBSN42FNRge2yBw3eQ88j9EMI031v2-d6DhlPGkZ2_UA9wWnhdYex3Xsjstefm6Xg1gvn_7L-sAl8-13AKxkK_NBLflZ2Em69VytZxfPlyVtLFjtHO2TTl30ld8Ui7T1oZ8xQu6Xe8DegEKmJiw55-HMYOMBQKB5kguN06fHmI4rslNcu7z_f5Z34aIA',
        'sandbox' => false,
    ];

    /**
     * Free shipping configuration - DESABILITADO
     */
    public array $freeShipping = [
        'enabled' => false,
        'min_value' => 0,
        'regions' => [],
        'services' => [],
    ];

    /**
     * Additional handling time (days)
     * Produtos retirados no Paraguai: terça, quinta e sexta
     */
    public int $handlingTime = 3;

    /**
     * Package default dimensions (cm)
     */
    public array $defaultPackage = [
        'width' => 11,
        'height' => 2,
        'length' => 16,
        'weight' => 0.3, // kg
    ];

    /**
     * Maximum package dimensions (cm)
     */
    public array $maxPackage = [
        'width' => 105,
        'height' => 105,
        'length' => 105,
        'weight' => 30, // kg
    ];

    public function __construct()
    {
        parent::__construct();

        // Load from .env
        $this->originZipCode = env('shipping.originZipCode', '87140000');

        // Correios
        $this->correios['usuario'] = env('correios.usuario', '');
        $this->correios['senha'] = env('correios.senha', '');
        $this->correios['codigo_empresa'] = env('correios.codigoEmpresa', '');

        // Melhor Envio
        $this->melhorenvio['client_id'] = env('melhorenvio.client_id', $this->melhorenvio['client_id']);
        $this->melhorenvio['client_secret'] = env('melhorenvio.client_secret', $this->melhorenvio['client_secret']);
        $this->melhorenvio['access_token'] = env('melhorenvio.access_token', $this->melhorenvio['access_token']);
        $this->melhorenvio['sandbox'] = env('melhorenvio.sandbox', $this->melhorenvio['sandbox']);
    }
}
