<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\OpenAIService;

class ExtractDimensions extends BaseCommand
{
    protected $group       = 'Products';
    protected $name        = 'products:dimensions';
    protected $description = 'Extrai dimensoes dos produtos usando ChatGPT';
    protected $usage       = 'products:dimensions [options]';
    protected $arguments   = [];
    protected $options     = [
        '-l' => 'Limite de produtos a processar',
        '-f' => 'Forcar atualizacao mesmo se ja tiver dimensoes',
    ];

    public function run(array $params)
    {
        $limit = (int) ($params['l'] ?? $params['limit'] ?? 100);
        $force = isset($params['f']) || isset($params['force']);

        $openai = new OpenAIService();
        $productModel = model('ProductModel');

        CLI::write('Extraindo dimensoes dos produtos...', 'green');
        CLI::write("Limite: {$limit} produtos", 'yellow');

        // Get products without dimensions or with default values
        $builder = $productModel->builder();

        if (!$force) {
            $builder->groupStart()
                    ->where('weight', 0)
                    ->orWhere('width', 0)
                    ->orWhere('height', 0)
                    ->orWhere('length', 0)
                    ->groupEnd();
        }

        $products = $builder->where('status', 'active')
                           ->limit($limit)
                           ->get()
                           ->getResultArray();

        $total = count($products);
        CLI::write("Encontrados: {$total} produtos para processar", 'yellow');

        if ($total === 0) {
            CLI::write('Nenhum produto para processar.', 'green');
            return;
        }

        $processed = 0;
        $errors = 0;

        foreach ($products as $index => $product) {
            $progress = $index + 1;
            CLI::showProgress($progress, $total);

            try {
                $dimensions = $openai->extractDimensions(
                    $product['name'],
                    $product['especificacoes'] ?? null
                );

                if ($dimensions) {
                    $productModel->update($product['id'], [
                        'weight' => $dimensions['weight'],
                        'width' => $dimensions['width'],
                        'height' => $dimensions['height'],
                        'length' => $dimensions['length'],
                    ]);
                    $processed++;
                } else {
                    $errors++;
                }

                // Rate limiting - 3 requests per second max
                usleep(350000); // 350ms

            } catch (\Exception $e) {
                $errors++;
                CLI::write("\nErro no produto {$product['id']}: " . $e->getMessage(), 'red');
            }
        }

        CLI::showProgress($total, $total);
        CLI::newLine(2);

        CLI::write("Processados: {$processed}", 'green');
        CLI::write("Erros: {$errors}", $errors > 0 ? 'red' : 'green');
        CLI::write('Concluido!', 'green');
    }
}
