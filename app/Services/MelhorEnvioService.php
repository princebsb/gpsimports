<?php

namespace App\Services;

class MelhorEnvioService
{
    protected string $token;
    protected string $baseUrl;
    protected string $cepOrigem;

    public function __construct()
    {
        $this->token = env('melhorenvio.token', '');
        $this->cepOrigem = env('melhorenvio.cepOrigem', '01310100');

        // Sandbox or production URL
        $sandbox = env('melhorenvio.sandbox', true);
        $this->baseUrl = $sandbox
            ? 'https://sandbox.melhorenvio.com.br/api/v2'
            : 'https://melhorenvio.com.br/api/v2';
    }

    /**
     * Calculate shipping for products
     */
    public function calculate(string $cepDestino, array $products): array
    {
        // If no token, use fallback calculation
        if (empty($this->token)) {
            return $this->fallbackCalculation($cepDestino, $products);
        }

        // Prepare products for API
        $apiProducts = [];
        $totalValue = 0;

        foreach ($products as $product) {
            $apiProducts[] = [
                'id' => $product['id'] ?? uniqid(),
                'width' => (int) ($product['width'] ?? 11),
                'height' => (int) ($product['height'] ?? 2),
                'length' => (int) ($product['length'] ?? 16),
                'weight' => (float) ($product['weight'] ?? 0.3),
                'insurance_value' => (float) ($product['price'] ?? 0),
                'quantity' => (int) ($product['quantity'] ?? 1),
            ];
            $totalValue += ($product['price'] ?? 0) * ($product['quantity'] ?? 1);
        }

        $payload = [
            'from' => ['postal_code' => $this->cepOrigem],
            'to' => ['postal_code' => preg_replace('/[^0-9]/', '', $cepDestino)],
            'products' => $apiProducts,
            'options' => [
                'insurance_value' => $totalValue,
                'receipt' => false,
                'own_hand' => false,
            ],
            'services' => '1,2' // PAC, SEDEX
        ];

        try {
            $response = $this->request('POST', '/me/shipment/calculate', $payload);

            if (empty($response)) {
                return $this->fallbackCalculation($cepDestino, $products);
            }

            return $this->formatResponse($response, $totalValue);

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio Error: ' . $e->getMessage());
            return $this->fallbackCalculation($cepDestino, $products);
        }
    }

    /**
     * Make API request
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $ch = curl_init();

        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
            'User-Agent: GPSImports/1.0',
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
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        if ($httpCode >= 400) {
            throw new \Exception('API Error: HTTP ' . $httpCode);
        }

        return json_decode($response, true) ?? [];
    }

    /**
     * Format API response
     */
    protected function formatResponse(array $response, float $totalValue): array
    {
        $options = [];

        foreach ($response as $service) {
            // Skip if error or no price
            if (!empty($service['error']) || empty($service['price'])) {
                continue;
            }

            $price = (float) $service['price'];

            $options[] = [
                'code' => $service['id'] ?? '',
                'name' => $service['name'] ?? 'Envio',
                'company' => $service['company']['name'] ?? 'Transportadora',
                'price' => $price,
                'deadline' => (int) ($service['delivery_time'] ?? 10),
                'logo' => $service['company']['picture'] ?? null,
            ];
        }

        // Sort by price
        usort($options, fn($a, $b) => $a['price'] <=> $b['price']);

        return $options;
    }

    /**
     * Fallback calculation when API is not available
     */
    protected function fallbackCalculation(string $cepDestino, array $products): array
    {
        $cep = preg_replace('/[^0-9]/', '', $cepDestino);
        $region = substr($cep, 0, 1);

        // Calculate total weight and value
        $totalWeight = 0;
        $totalValue = 0;
        $maxWidth = 0;
        $maxHeight = 0;
        $maxLength = 0;

        foreach ($products as $product) {
            $qty = (int) ($product['quantity'] ?? 1);
            $totalWeight += (float) ($product['weight'] ?? 0.3) * $qty;
            $totalValue += (float) ($product['price'] ?? 0) * $qty;
            $maxWidth = max($maxWidth, (int) ($product['width'] ?? 11));
            $maxHeight = max($maxHeight, (int) ($product['height'] ?? 2));
            $maxLength = max($maxLength, (int) ($product['length'] ?? 16));
        }

        // Region multiplier
        $regionMultiplier = match($region) {
            '0', '1' => 1.0,   // SP
            '2' => 1.15,       // RJ, ES
            '3' => 1.25,       // MG
            '4' => 1.35,       // BA, SE
            '5' => 1.45,       // PE, AL, PB, RN
            '6' => 1.60,       // CE, PI, MA, PA, AP, AM
            '7' => 1.40,       // DF, GO, TO, MT, MS, RO, AC
            '8' => 1.20,       // PR, SC
            '9' => 1.30,       // RS
            default => 1.35,
        };

        // Calculate base price (weight + volume)
        $volumetricWeight = ($maxWidth * $maxHeight * $maxLength) / 6000;
        $chargeableWeight = max($totalWeight, $volumetricWeight);
        $basePrice = max(18, $chargeableWeight * 12);

        $options = [];

        // PAC
        $pacPrice = round($basePrice * $regionMultiplier, 2);
        $pacDeadline = 7 + (int) $region;

        $options[] = [
            'code' => 'PAC',
            'name' => 'PAC',
            'company' => 'Correios',
            'price' => $pacPrice,
            'deadline' => $pacDeadline,
            'logo' => null,
        ];

        // SEDEX
        $sedexPrice = round($basePrice * $regionMultiplier * 1.9, 2);
        $sedexDeadline = 2 + (int) floor((int) $region / 3);

        $options[] = [
            'code' => 'SEDEX',
            'name' => 'SEDEX',
            'company' => 'Correios',
            'price' => $sedexPrice,
            'deadline' => $sedexDeadline,
            'logo' => null,
        ];

        return $options;
    }
}
