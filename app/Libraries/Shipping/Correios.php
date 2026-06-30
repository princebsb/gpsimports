<?php

namespace App\Libraries\Shipping;

use Config\Shipping;

class Correios implements ShippingInterface
{
    protected Shipping $config;
    protected string $apiUrl = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';

    public function __construct()
    {
        $this->config = config('Shipping');
    }

    public function calculate(array $params): array
    {
        $queryParams = [
            'nCdEmpresa' => $this->config->correios['codigo_empresa'] ?? '',
            'sDsSenha' => $this->config->correios['senha'] ?? '',
            'nCdServico' => $params['service_code'],
            'sCepOrigem' => preg_replace('/[^0-9]/', '', $params['origin_zipcode']),
            'sCepDestino' => preg_replace('/[^0-9]/', '', $params['destination_zipcode']),
            'nVlPeso' => $params['weight'] ?? 0.3,
            'nCdFormato' => 1,
            'nVlComprimento' => $params['length'] ?? 16,
            'nVlAltura' => $params['height'] ?? 2,
            'nVlLargura' => $params['width'] ?? 11,
            'nVlDiametro' => 0,
            'sCdMaoPropria' => 'N',
            'nVlValorDeclarado' => 0,
            'sCdAvisoRecebimento' => 'N',
            'StrRetorno' => 'xml',
        ];

        $url = $this->apiUrl . '?' . http_build_query($queryParams);

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_CONNECTTIMEOUT => 1,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || empty($response)) {
                return $this->getFallbackPrice($params);
            }

            $xml = @simplexml_load_string($response);
            if (!$xml || !isset($xml->cServico)) {
                return $this->getFallbackPrice($params);
            }

            $service = $xml->cServico;
            if ((string) $service->Erro !== '0' && (string) $service->Erro !== '') {
                return $this->getFallbackPrice($params);
            }

            return [
                'success' => true,
                'price' => (float) str_replace(',', '.', (string) $service->Valor),
                'days' => (int) $service->PrazoEntrega,
                'description' => $this->getServiceName($params['service_code']),
            ];
        } catch (\Exception $e) {
            return $this->getFallbackPrice($params);
        }
    }

    public function tracking(string $trackingCode): array
    {
        return [
            'success' => true,
            'tracking_code' => $trackingCode,
            'events' => [],
            'message' => 'Use o site dos Correios para rastrear.',
        ];
    }

    public function validateZipcode(string $zipcode): bool
    {
        return strlen(preg_replace('/[^0-9]/', '', $zipcode)) === 8;
    }

    protected function getFallbackPrice(array $params): array
    {
        $weight = $params['weight'] ?? 0.3;
        $code = $params['service_code'];

        $basePrice = match ($code) {
            '04014' => 25.00,
            '04510' => 18.00,
            default => 20.00,
        };

        return [
            'success' => true,
            'price' => round($basePrice + ($weight * 3), 2),
            'days' => $code === '04014' ? 5 : 10,
            'description' => $this->getServiceName($code),
            'fallback' => true,
        ];
    }

    protected function getServiceName(string $code): string
    {
        return match ($code) {
            '04014' => 'SEDEX',
            '04510' => 'PAC',
            default => 'Correios',
        };
    }
}
