<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class ExchangeRateCommand extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'exchange:check';
    protected $description = 'Verifica cotacao do dolar no AtacadoConnect';

    public function run(array $params)
    {
        $db = Database::connect();

        // Buscar um produto que tenha URL de origem
        $product = $db->table('products')
            ->select('url_origem')
            ->where('url_origem IS NOT NULL')
            ->where('url_origem !=', '')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($product) {
            CLI::write("URL exemplo: " . $product['url_origem']);
        }

        // Tentar buscar cotacao via curl
        CLI::write("\nTentando buscar cotacao do AtacadoConnect...", 'yellow');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://atacadoconnect.com/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        CLI::write("HTTP Code: {$httpCode}");

        if ($html) {
            // Procurar por cotacao no HTML
            // Padroes comuns: "R$ X,XX", "cotacao", "dolar"
            if (preg_match('/[Cc]ota[çc][aã]o[^0-9]*R?\$?\s*([0-9]+[,\.][0-9]+)/u', $html, $matches)) {
                CLI::write("Cotacao encontrada: R$ " . $matches[1], 'green');
            } elseif (preg_match('/R\$\s*([0-9]+[,\.][0-9]+)/', $html, $matches)) {
                CLI::write("Valor BRL encontrado: R$ " . $matches[1]);
            }

            // Procurar em scripts JSON
            if (preg_match('/["\'](cotacao|exchangeRate|dollarRate)["\']:\s*([0-9]+\.?[0-9]*)/i', $html, $matches)) {
                CLI::write("Cotacao em JSON: " . $matches[2], 'green');
            }

            // Mostrar trecho relevante
            $pos = stripos($html, 'cotação');
            if ($pos !== false) {
                CLI::write("\nTrecho com 'cotacao':");
                CLI::write(substr($html, max(0, $pos - 50), 200));
            }
        }
    }
}
