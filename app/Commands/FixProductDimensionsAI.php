<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixProductDimensionsAI extends BaseCommand
{
    protected $group       = 'Products';
    protected $name        = 'products:fix-dimensions-ai';
    protected $description = 'Corrige pesos e dimensoes usando ChatGPT API';

    protected $apiKey;
    protected $batchSize = 5; // Processar 5 produtos por vez para maior precisão

    public function run(array $params)
    {
        $this->apiKey = env('OPENAI_API_KEY');

        if (empty($this->apiKey)) {
            CLI::error('OPENAI_API_KEY nao configurada no .env');
            return;
        }

        $db = \Config\Database::connect();
        $limit = $params[0] ?? null;
        $offset = $params[1] ?? 0;

        // Buscar produtos ativos
        $query = $db->table('products')
            ->select('id, sku, name, weight, width, height, length, marca')
            ->where('status', 'active');

        if ($limit) {
            $query->limit((int)$limit, (int)$offset);
        }

        $products = $query->get()->getResultArray();
        $total = count($products);

        CLI::write("Processando {$total} produtos com ChatGPT...", 'white');
        CLI::newLine();

        $updated = 0;
        $errors = 0;
        $batches = array_chunk($products, $this->batchSize);

        foreach ($batches as $batchIndex => $batch) {
            CLI::write("Lote " . ($batchIndex + 1) . "/" . count($batches) . "...", 'yellow');

            $result = $this->getProductDimensionsFromAI($batch);

            if ($result) {
                foreach ($result as $productData) {
                    if (isset($productData['sku']) && isset($productData['weight'])) {
                        // Encontrar o produto pelo SKU
                        $product = array_filter($batch, fn($p) => $p['sku'] == $productData['sku']);
                        $product = reset($product);

                        if ($product) {
                            $weight = (float) $productData['weight'];
                            $width = (float) ($productData['width'] ?? 10);
                            $height = (float) ($productData['height'] ?? 10);
                            $length = (float) ($productData['length'] ?? 5);

                            // Validar valores razoáveis (até 150kg para produtos grandes)
                            if ($weight > 0 && $weight < 150 && $width > 0 && $height > 0 && $length > 0) {
                                $this->updateProduct($db, $product['id'], [
                                    'weight' => $weight,
                                    'width' => $width,
                                    'height' => $height,
                                    'length' => $length,
                                ]);

                                $updated++;
                                CLI::write("  OK: {$product['sku']} - {$weight}kg {$width}x{$height}x{$length}cm", 'green');
                            } else {
                                CLI::write("  SKIP: {$product['sku']} - valores invalidos", 'yellow');
                            }
                        }
                    }
                }
            } else {
                $errors++;
                CLI::write("  ERRO no lote", 'red');
            }

            // Pausa para evitar rate limit
            if ($batchIndex < count($batches) - 1) {
                sleep(1);
            }
        }

        CLI::newLine();
        CLI::write("Resultado:", 'white');
        CLI::write("  Total: {$total}", 'white');
        CLI::write("  Atualizados: {$updated}", 'green');
        CLI::write("  Erros: {$errors}", 'red');
    }

    protected function getProductDimensionsFromAI(array $products): ?array
    {
        $productList = [];
        foreach ($products as $p) {
            $productList[] = [
                'sku' => $p['sku'],
                'name' => $p['name'],
                'brand' => $p['marca'] ?? '',
            ];
        }

        $prompt = "Você é um especialista em logística e especificações técnicas de produtos.

TAREFA: Para cada produto, forneça o PESO BRUTO (com embalagem) em KG e DIMENSÕES DA CAIXA em CM para envio.

INSTRUÇÕES CRÍTICAS:
1. Use dados REAIS e ESPECÍFICOS do produto baseado no modelo exato
2. NÃO use valores genéricos - pesquise mentalmente as especificações reais
3. Considere o TAMANHO REAL do produto:
   - JBL PartyBox Ultimate: ~44kg, 53x115x53cm (é uma caixa de som GIGANTE)
   - JBL PartyBox 310: ~17kg, 32x68x32cm
   - JBL PartyBox 110: ~10kg, 30x53x28cm
   - JBL Boombox 3: ~6.7kg, 48x25x20cm
   - iPhone/Smartphones: ~0.3kg, 18x10x5cm
   - MacBook Pro 16\": ~2.5kg, 42x30x6cm
   - iMac 24\": ~5kg, 65x55x25cm
   - TV 55\": ~18kg, 140x85x15cm
   - TV 65\": ~25kg, 160x95x18cm
   - Impressora Laser grande: ~25kg, 60x50x45cm
   - Ar condicionado split: ~35kg, 100x40x35cm

4. Para caixas de som PARTYBOX, são produtos MUITO GRANDES e PESADOS
5. Para TVs, considere o tamanho da tela em polegadas

Produtos para analisar:
" . json_encode($productList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "

Responda APENAS com JSON array válido:
[
  {\"sku\": \"123\", \"weight\": 44.5, \"width\": 53, \"height\": 115, \"length\": 53},
  ...
]";

        $response = $this->callOpenAI($prompt);

        if ($response) {
            // Extrair JSON da resposta
            preg_match('/\[[\s\S]*\]/', $response, $matches);
            if (!empty($matches[0])) {
                $data = json_decode($matches[0], true);
                if (is_array($data)) {
                    return $data;
                }
            }
        }

        return null;
    }

    protected function updateProduct($db, int $id, array $data): bool
    {
        try {
            // Reconectar se necessário
            if (!$db->connID || !mysqli_ping($db->connID)) {
                $db->reconnect();
            }

            return $db->table('products')
                ->where('id', $id)
                ->update($data);
        } catch (\Exception $e) {
            CLI::write("  DB Error: " . $e->getMessage(), 'red');
            $db->reconnect();
            return false;
        }
    }

    protected function callOpenAI(string $prompt): ?string
    {
        $ch = curl_init('https://api.openai.com/v1/chat/completions');

        $data = [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.3,
            'max_tokens' => 2000,
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return $result['choices'][0]['message']['content'] ?? null;
        }

        CLI::write("API Error: HTTP {$httpCode}", 'red');
        if ($response) {
            $error = json_decode($response, true);
            CLI::write("  " . ($error['error']['message'] ?? $response), 'red');
        }

        return null;
    }
}
