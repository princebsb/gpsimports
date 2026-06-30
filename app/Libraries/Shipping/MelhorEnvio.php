<?php

namespace App\Libraries\Shipping;

class MelhorEnvio implements ShippingInterface
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $accessToken;
    protected string $userAgent;
    protected bool $sandbox;
    protected string $baseUrl;

    public function __construct()
    {
        $config = config('Shipping');

        $this->clientId = $config->melhorenvio['client_id'];
        $this->clientSecret = $config->melhorenvio['client_secret'];
        $this->accessToken = $config->melhorenvio['access_token'];
        $this->userAgent = $config->melhorenvio['user_agent'] ?? 'GPS Imports';
        $this->sandbox = $config->melhorenvio['sandbox'];

        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.melhorenvio.com.br/api/v2'
            : 'https://melhorenvio.com.br/api/v2';
    }

    /**
     * Calcular frete
     */
    public function calculate(array $params): array
    {
        $originZipcode = preg_replace('/[^0-9]/', '', $params['origin_zipcode']);
        $destinationZipcode = preg_replace('/[^0-9]/', '', $params['destination_zipcode']);

        // Construir array de produtos
        $products = [];
        if (!empty($params['items'])) {
            foreach ($params['items'] as $item) {
                $products[] = [
                    'id' => (string) $item['product_id'],
                    'width' => max(1, (int) ($item['width'] ?? 11)),
                    'height' => max(1, (int) ($item['height'] ?? 2)),
                    'length' => max(1, (int) ($item['length'] ?? 16)),
                    'weight' => max(0.1, (float) ($item['weight'] ?? 0.3)),
                    'insurance_value' => (float) ($item['price'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? 1),
                ];
            }
        } else {
            $products[] = [
                'id' => '1',
                'width' => max(1, (int) ($params['width'] ?? 11)),
                'height' => max(1, (int) ($params['height'] ?? 2)),
                'length' => max(1, (int) ($params['length'] ?? 16)),
                'weight' => max(0.1, (float) ($params['weight'] ?? 0.3)),
                'insurance_value' => (float) ($params['insurance_value'] ?? 0),
                'quantity' => 1,
            ];
        }

        // Payload para calculo - com filtro de servicos Correios
        // IDs dos servicos: 1=PAC, 2=SEDEX, 3=Mini Envios, 4=Mini Envios Expressos, 17=Mini Envios
        $payload = [
            'from' => [
                'postal_code' => $originZipcode,
            ],
            'to' => [
                'postal_code' => $destinationZipcode,
            ],
            'products' => $products,
            'services' => '1,2', // Apenas PAC e SEDEX dos Correios
        ];

        // Log para debug
        log_message('debug', 'MelhorEnvio::calculate - Payload: ' . json_encode($payload));

        $response = $this->request('POST', '/me/shipment/calculate', $payload);

        // Log resultado
        log_message('debug', 'MelhorEnvio::calculate - Response: ' . json_encode($response));

        if (!$response['success']) {
            return $response;
        }

        return [
            'success' => true,
            'options' => $this->formatOptions($response['data']),
        ];
    }

    /**
     * Formatar opcoes de frete
     */
    protected function formatOptions(array $data): array
    {
        $options = [];

        // IDs dos servicos Correios que queremos
        // 1 = PAC, 2 = SEDEX
        $correiosServiceIds = [1, 2];

        // Log todos os servicos disponiveis
        foreach ($data as $service) {
            $serviceId = $service['id'] ?? 0;
            $companyName = $service['company']['name'] ?? 'N/A';
            $serviceName = $service['name'] ?? 'N/A';
            $price = $service['price'] ?? 0;
            $error = $service['error'] ?? null;
            log_message('debug', "MelhorEnvio Service ID {$serviceId}: {$serviceName} - {$companyName} - R$ {$price}" . ($error ? " (Error: {$error})" : ""));
        }

        foreach ($data as $service) {
            $serviceId = (int) ($service['id'] ?? 0);

            // Pular servicos com erro
            if (!empty($service['error'])) {
                continue;
            }

            // Pular se nao tem preco
            if (empty($service['price']) || $service['price'] <= 0) {
                continue;
            }

            // Filtrar apenas Correios PAC (1) e SEDEX (2) pelo ID do servico
            if (!in_array($serviceId, $correiosServiceIds)) {
                continue;
            }

            $serviceName = $service['name'] ?? 'Envio';
            $companyName = $service['company']['name'] ?? 'Correios';

            $options[] = [
                'code' => $serviceId . '_' . ($service['company']['id'] ?? ''),
                'name' => $serviceName,
                'company' => $companyName,
                'company_picture' => $service['company']['picture'] ?? '',
                'price' => (float) $service['price'],
                'delivery_time' => (int) ($service['delivery_time'] ?? 0),
            ];
        }

        log_message('debug', 'MelhorEnvio Filtered Options: ' . json_encode($options));

        // Ordenar por preco
        usort($options, fn($a, $b) => $a['price'] <=> $b['price']);

        return $options;
    }

    /**
     * Fazer requisicao na API
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: ' . $this->userAgent,
        ];

        if (!empty($this->accessToken)) {
            $headers[] = 'Authorization: Bearer ' . $this->accessToken;
        }

        $ch = curl_init();

        // Em producao, SSL_VERIFYPEER deve ser true
        $verifySSL = ENVIRONMENT === 'production';

        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => $verifySSL,
            CURLOPT_SSL_VERIFYHOST => $verifySSL ? 2 : 0,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method === 'POST') {
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            log_message('error', 'MelhorEnvio::request - cURL error: ' . $error);
            return ['success' => false, 'message' => 'Erro de conexao.'];
        }

        $responseData = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMessage = $responseData['message'] ?? $responseData['error'] ?? 'Erro na API do Melhor Envio.';
            log_message('error', 'MelhorEnvio::request - HTTP ' . $httpCode . ': ' . $errorMessage . ' | Response: ' . $response);
            return ['success' => false, 'message' => $errorMessage];
        }

        return ['success' => true, 'data' => $responseData];
    }

    /**
     * Rastreamento
     */
    public function tracking(string $trackingCode): array
    {
        $response = $this->request('GET', '/me/shipment/tracking/' . urlencode($trackingCode));

        if (!$response['success']) {
            return $response;
        }

        return [
            'success' => true,
            'tracking' => $response['data'],
        ];
    }
}
