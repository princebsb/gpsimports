<?php

namespace App\Services;

use Config\Database;

class ExchangeRateService
{
    protected $db;
    protected $cacheKey = 'usd_exchange_rate';
    protected $cacheTime = 3600; // 1 hora

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtem a cotacao atual do dolar
     * Prioridade: 1) Config manual, 2) Cache, 3) API BCB
     */
    public function getRate(): float
    {
        // 1. Verificar se existe configuracao manual
        $setting = $this->db->table('settings')
            ->where('key', 'usd_exchange_rate')
            ->get()
            ->getRowArray();

        if ($setting && (float) $setting['value'] > 0) {
            return (float) $setting['value'];
        }

        // 2. Verificar cache
        $cache = $this->db->table('settings')
            ->where('key', 'usd_exchange_rate_cache')
            ->get()
            ->getRowArray();

        if ($cache) {
            $data = json_decode($cache['value'], true);
            if ($data && isset($data['rate'], $data['updated_at'])) {
                $cacheAge = time() - strtotime($data['updated_at']);
                if ($cacheAge < $this->cacheTime) {
                    return (float) $data['rate'];
                }
            }
        }

        // 3. Buscar da API do Banco Central
        $rate = $this->fetchFromBCB();

        if ($rate > 0) {
            $this->cacheRate($rate);
            return $rate;
        }

        // Fallback
        return 5.50;
    }

    /**
     * Busca cotacao do Banco Central do Brasil
     */
    protected function fetchFromBCB(): float
    {
        try {
            // API do BCB - Cotacao de fechamento do dolar
            $date = date('m-d-Y');
            $url = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoDolarDia(dataCotacao=@dataCotacao)?@dataCotacao='{$date}'&\$format=json";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (isset($data['value'][0]['cotacaoVenda'])) {
                    return (float) $data['value'][0]['cotacaoVenda'];
                }
            }

            // Tentar dia anterior se nao houver cotacao hoje (fim de semana/feriado)
            return $this->fetchFromBCBPreviousDays();

        } catch (\Exception $e) {
            log_message('error', 'ExchangeRateService: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Busca cotacao dos ultimos dias uteis
     */
    protected function fetchFromBCBPreviousDays(): float
    {
        for ($i = 1; $i <= 5; $i++) {
            $date = date('m-d-Y', strtotime("-{$i} days"));
            $url = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoDolarDia(dataCotacao=@dataCotacao)?@dataCotacao='{$date}'&\$format=json";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (isset($data['value'][0]['cotacaoVenda'])) {
                    return (float) $data['value'][0]['cotacaoVenda'];
                }
            }
        }

        return 0;
    }

    /**
     * Salva cotacao no cache
     */
    protected function cacheRate(float $rate): void
    {
        $data = json_encode([
            'rate' => $rate,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $exists = $this->db->table('settings')
            ->where('key', 'usd_exchange_rate_cache')
            ->countAllResults();

        if ($exists) {
            $this->db->table('settings')
                ->where('key', 'usd_exchange_rate_cache')
                ->update(['value' => $data]);
        } else {
            $this->db->table('settings')->insert([
                'key' => 'usd_exchange_rate_cache',
                'value' => $data,
                'type' => 'json',
            ]);
        }
    }

    /**
     * Define cotacao manual
     */
    public function setManualRate(float $rate): bool
    {
        $exists = $this->db->table('settings')
            ->where('key', 'usd_exchange_rate')
            ->countAllResults();

        if ($exists) {
            return $this->db->table('settings')
                ->where('key', 'usd_exchange_rate')
                ->update(['value' => (string) $rate]);
        }

        return $this->db->table('settings')->insert([
            'key' => 'usd_exchange_rate',
            'value' => (string) $rate,
            'type' => 'number',
        ]);
    }

    /**
     * Limpa cotacao manual (usa API)
     */
    public function clearManualRate(): bool
    {
        return $this->db->table('settings')
            ->where('key', 'usd_exchange_rate')
            ->update(['value' => '0']);
    }
}
