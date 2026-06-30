<?php

namespace App\Services;

use App\Libraries\Shipping\Correios;
use App\Libraries\Shipping\MelhorEnvio;
use Config\Shipping as ShippingConfig;

class ShippingService
{
    protected ShippingConfig $config;
    protected ?Correios $correios = null;
    protected ?MelhorEnvio $melhorEnvio = null;

    public function __construct()
    {
        $this->config = config('Shipping');
    }

    protected function getCorreios(): Correios
    {
        if ($this->correios === null) {
            $this->correios = new Correios();
        }
        return $this->correios;
    }

    protected function getMelhorEnvio(): MelhorEnvio
    {
        if ($this->melhorEnvio === null) {
            $this->melhorEnvio = new MelhorEnvio();
        }
        return $this->melhorEnvio;
    }

    /**
     * Calcular opcoes de frete
     */
    public function calculate(string $zipcode, float $weight, float $cartTotal, array $items = []): array
    {
        $options = [];
        $zipcode = preg_replace('/[^0-9]/', '', $zipcode);

        if (strlen($zipcode) !== 8) {
            return [];
        }

        // Usar Melhor Envio se habilitado
        if ($this->config->providers['melhorenvio']['enabled']) {
            try {
                $melhorEnvio = $this->getMelhorEnvio();

                $result = $melhorEnvio->calculate([
                    'origin_zipcode' => $this->config->originZipCode,
                    'destination_zipcode' => $zipcode,
                    'items' => $items,
                    'weight' => $weight ?: 0.3,
                    'width' => $this->config->defaultPackage['width'],
                    'height' => $this->config->defaultPackage['height'],
                    'length' => $this->config->defaultPackage['length'],
                    'insurance_value' => $cartTotal,
                ]);

                if ($result['success'] && !empty($result['options'])) {
                    foreach ($result['options'] as $option) {
                        $options[] = [
                            'code' => $option['code'],
                            'name' => $option['name'],
                            'company' => $option['company'] ?? '',
                            'company_picture' => $option['company_picture'] ?? '',
                            'price' => $option['price'],
                            'deadline' => $option['delivery_time'] + $this->config->handlingTime,
                            'description' => $option['company'] ?? '',
                        ];
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'ShippingService::calculate MelhorEnvio - ' . $e->getMessage());
            }
        }

        // Se Melhor Envio retornou opcoes, nao precisa do Correios
        $hasMelhorEnvioOptions = count($options) > 0;

        // Usar Correios como fallback apenas se Melhor Envio nao retornou opcoes
        // e se Correios direto estiver habilitado (desabilitado por padrao quando usa Melhor Envio)
        $useCorreiosFallback = false; // Desabilitado - usar apenas Melhor Envio
        if (!$hasMelhorEnvioOptions && $useCorreiosFallback) {
            try {
                $correios = $this->getCorreios();

                foreach ($this->config->correiosServices as $code => $service) {
                    if (!$service['enabled']) {
                        continue;
                    }

                    $result = $correios->calculate([
                        'service_code' => $code,
                        'origin_zipcode' => $this->config->originZipCode,
                        'destination_zipcode' => $zipcode,
                        'weight' => $weight ?: 0.3,
                        'width' => $this->config->defaultPackage['width'],
                        'height' => $this->config->defaultPackage['height'],
                        'length' => $this->config->defaultPackage['length'],
                    ]);

                    if ($result['success']) {
                        $options[] = [
                            'code' => $code,
                            'name' => $service['name'],
                            'company' => 'Correios',
                            'price' => $result['price'],
                            'deadline' => $result['days'] + $this->config->handlingTime,
                            'description' => $result['description'] ?? '',
                        ];
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'ShippingService::calculate Correios - ' . $e->getMessage());
            }
        }

        // Ordenar por preco
        usort($options, fn($a, $b) => $a['price'] <=> $b['price']);

        return $options;
    }

    /**
     * Buscar opcao por codigo
     */
    public function getByCode(string $code, string $zipcode, float $weight, array $items = []): ?array
    {
        $options = $this->calculate($zipcode, $weight, 0, $items);

        foreach ($options as $option) {
            if ($option['code'] === $code) {
                return $option;
            }
        }

        return null;
    }

    /**
     * Validar CEP
     */
    public function validateZipcode(string $zipcode): array
    {
        $zipcode = preg_replace('/[^0-9]/', '', $zipcode);

        if (strlen($zipcode) !== 8) {
            return ['valid' => false, 'message' => 'CEP invalido.'];
        }

        $url = "https://viacep.com.br/ws/{$zipcode}/json/";

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                return ['valid' => false, 'message' => 'Erro ao consultar CEP.'];
            }

            $data = json_decode($response, true);

            if (isset($data['erro'])) {
                return ['valid' => false, 'message' => 'CEP nao encontrado.'];
            }

            return [
                'valid' => true,
                'address' => [
                    'zipcode' => $data['cep'],
                    'street' => $data['logradouro'],
                    'neighborhood' => $data['bairro'],
                    'city' => $data['localidade'],
                    'state' => $data['uf'],
                ],
            ];
        } catch (\Exception $e) {
            return ['valid' => false, 'message' => 'Erro ao consultar CEP.'];
        }
    }

    /**
     * Rastreamento
     */
    public function getTracking(string $trackingCode): array
    {
        if ($this->config->providers['melhorenvio']['enabled']) {
            $melhorEnvio = $this->getMelhorEnvio();
            return $melhorEnvio->tracking($trackingCode);
        }

        $correios = $this->getCorreios();
        return $correios->tracking($trackingCode);
    }

    /**
     * Verificar disponibilidade de entrega
     */
    public function isDeliveryAvailable(string $zipcode): bool
    {
        $options = $this->calculate($zipcode, 0.3, 0);
        return !empty($options);
    }

    /**
     * Data estimada de entrega
     */
    public function getEstimatedDelivery(int $days): string
    {
        $date = new \DateTime();
        $daysAdded = 0;

        while ($daysAdded < $days) {
            $date->modify('+1 day');
            $dayOfWeek = $date->format('N');
            if ($dayOfWeek < 6) {
                $daysAdded++;
            }
        }

        return $date->format('Y-m-d');
    }

    /**
     * Formatar texto de entrega
     */
    public function formatDeliveryText(int $days): string
    {
        if ($days === 1) {
            return 'Entrega em 1 dia util';
        }
        return "Entrega em {$days} dias uteis";
    }
}
