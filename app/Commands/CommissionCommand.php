<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\CommissionService;
use Config\Database;

class CommissionCommand extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'commission';
    protected $description = 'Gerencia comissoes e recalcula precos dos produtos';
    protected $usage       = 'commission [action] [options]';
    protected $arguments   = [
        'action' => 'Acao: list, calculate, test, check',
    ];
    protected $options     = [
        '-l' => 'Limite de produtos para processar',
        '-p' => 'ID do produto especifico',
        '-s' => 'SKU do produto',
    ];

    public function run(array $params)
    {
        $action = $params[0] ?? 'list';

        switch ($action) {
            case 'list':
                $this->listCommissions();
                break;
            case 'calculate':
                $this->calculatePrices($params);
                break;
            case 'test':
                $this->testCalculation($params);
                break;
            case 'check':
                $this->checkProduct($params);
                break;
            default:
                CLI::error("Acao desconhecida: {$action}");
                CLI::write('Acoes disponiveis: list, calculate, test');
        }
    }

    protected function listCommissions()
    {
        $db = Database::connect();
        $commissions = $db->table('commissions')
            ->where('active', 1)
            ->orderBy('product_type')
            ->orderBy('price_min_usd')
            ->get()
            ->getResultArray();

        CLI::write('Tabela de Comissoes:', 'green');
        CLI::write(str_repeat('-', 90));

        $currentType = '';
        foreach ($commissions as $c) {
            if ($c['product_type'] !== $currentType) {
                $currentType = $c['product_type'];
                CLI::newLine();
                CLI::write(strtoupper($currentType), 'yellow');
            }

            $range = sprintf('$%.2f - %s',
                $c['price_min_usd'],
                $c['price_max_usd'] ? sprintf('$%.2f', $c['price_max_usd']) : 'sem limite'
            );

            $status = $c['is_blocked'] ? CLI::color('BLOQUEADO', 'red') : sprintf('%.0f%% + %.0f%%', $c['commission_rate'], $c['platform_fee']);

            CLI::write(sprintf('  %-25s | %s | %s', $range, $status, $c['notes'] ?? ''));
        }

        CLI::newLine();
        CLI::write('Total: ' . count($commissions) . ' regras de comissao', 'cyan');
    }

    protected function calculatePrices(array $params)
    {
        $limit = CLI::getOption('l') ?? 100;
        $productId = CLI::getOption('p');

        $service = new CommissionService();
        $db = Database::connect();

        // Busca produtos
        $builder = $db->table('products')
            ->select('id, name, price, cost_price, category_id')
            ->where('status', 'active');

        if ($productId) {
            $builder->where('id', $productId);
        } else {
            $builder->where('cost_price >', 0)
                ->limit((int) $limit);
        }

        $products = $builder->get()->getResultArray();

        if (empty($products)) {
            CLI::error('Nenhum produto encontrado com cost_price > 0');
            return;
        }

        CLI::write(sprintf('Processando %d produtos...', count($products)), 'green');

        $updated = 0;
        $blocked = 0;
        $errors = 0;

        foreach ($products as $product) {
            // Busca categoria
            $category = null;
            if ($product['category_id']) {
                $cat = $db->table('categories')
                    ->select('name')
                    ->where('id', $product['category_id'])
                    ->get()
                    ->getRowArray();
                $category = $cat['name'] ?? null;
            }

            $costUsd = (float) $product['cost_price'];
            $productType = $service->detectProductType($product['name'], $category);
            $calc = $service->calculateFinalPrice($costUsd, $productType);

            if ($calc['is_blocked']) {
                $blocked++;
                CLI::write(sprintf('[BLOQUEADO] #%d %s (custo: $%.2f)',
                    $product['id'],
                    substr($product['name'], 0, 50),
                    $costUsd
                ), 'red');
                continue;
            }

            // Atualiza preco
            $db->table('products')
                ->where('id', $product['id'])
                ->update([
                    'price' => $calc['final_price_brl'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $updated++;

            CLI::write(sprintf('#%d | %s | Tipo: %s | Custo: $%.2f | Preco: R$ %.2f',
                $product['id'],
                substr($product['name'], 0, 35),
                $productType,
                $costUsd,
                $calc['final_price_brl']
            ));
        }

        CLI::newLine();
        CLI::write('Resumo:', 'green');
        CLI::write("  Atualizados: {$updated}");
        CLI::write("  Bloqueados: {$blocked}");
        CLI::write("  Erros: {$errors}");
    }

    protected function testCalculation(array $params)
    {
        $service = new CommissionService();
        $db = Database::connect();

        CLI::write('Teste de Calculo de Precos:', 'green');
        CLI::write(str_repeat('-', 70));

        // Taxa de cambio
        $settingRow = $db->table('settings')->where('key', 'usd_exchange_rate')->get()->getRowArray();
        $rate = (float) ($settingRow['value'] ?? 5.50);
        CLI::write("Taxa de cambio: USD 1.00 = BRL {$rate}");
        CLI::newLine();

        // Testes
        $tests = [
            ['iPhone 17 Pro Max 256GB', 'celulares', 1199.00],
            ['iPhone 16 Pro 128GB', 'celulares', 999.00],
            ['iPhone 15 Pro Max CPO', 'celulares', 850.00],
            ['MacBook Pro M3 14"', 'computadores', 1999.00],
            ['iPad Pro 12.9" M2', 'tablets', 1099.00],
            ['AirPods Pro 2', 'fones', 249.00],
            ['Perfume Carolina Herrera 212 VIP', 'perfumes', 95.00],
            ['Perfume Barato Teste', 'perfumes', 5.00],
            ['Perfume Caro Importado 200ml', 'perfumes', 150.00],
            ['Creme Hidratante La Roche', 'cosmeticos', 35.00],
            ['Samsung Galaxy S24 Ultra', 'celulares', 120.00],
            ['Xiaomi Redmi Note 13', 'celulares', 75.00],
            ['PlayStation 5 Slim', 'games', 449.00],
            ['Fone JBL Tune 510BT', 'acessorios', 45.00],
            ['Chaleira Eletrica Xiaomi Smart Kettle', 'eletrodomesticos', 36.50],
            ['Fritadeira Xiaomi Smart Air Fryer 6L', 'eletrodomesticos', 68.00],
            ['Cafeteira Xiaomi Coffee Machine', 'eletrodomesticos', 85.00],
            ['Liquidificador Xiaomi Blender Pro', 'eletrodomesticos', 70.00],
        ];

        foreach ($tests as [$name, $cat, $cost]) {
            $type = $service->detectProductType($name, $cat);
            $calc = $service->calculateFinalPrice($cost, $type);

            $status = $calc['is_blocked']
                ? CLI::color('BLOQUEADO', 'red')
                : sprintf('R$ %.2f', $calc['final_price_brl']);

            CLI::write(sprintf('%-40s | Tipo: %-18s | $%.2f -> %s (%.0f%%+%.0f%%)',
                substr($name, 0, 40),
                $type,
                $cost,
                $status,
                $calc['commission_rate'],
                $calc['platform_fee']
            ));
        }
    }

    protected function checkProduct(array $params)
    {
        $sku = CLI::getOption('s') ?? ($params[1] ?? null);

        if (!$sku) {
            CLI::error('Informe o SKU do produto: commission check -s SKU');
            return;
        }

        $db = Database::connect();
        $service = new CommissionService();

        $product = $db->table('products')
            ->select('id, sku, name, price, cost_price, category_id')
            ->where('sku', $sku)
            ->get()
            ->getRowArray();

        if (!$product) {
            CLI::error("Produto com SKU {$sku} nao encontrado!");
            return;
        }

        CLI::write('=== PRODUTO ===', 'green');
        CLI::write("ID: {$product['id']}");
        CLI::write("SKU: {$product['sku']}");
        CLI::write("Nome: {$product['name']}");
        CLI::write("Preco atual: R$ " . number_format($product['price'], 2, ',', '.'));
        CLI::write("Custo (USD): $" . number_format($product['cost_price'] ?? 0, 2));
        CLI::newLine();

        // Buscar categoria
        $category = null;
        if ($product['category_id']) {
            $cat = $db->table('categories')
                ->select('name')
                ->where('id', $product['category_id'])
                ->get()
                ->getRowArray();
            $category = $cat['name'] ?? null;
        }

        CLI::write("Categoria: " . ($category ?? 'N/A'));

        $costUsd = (float) ($product['cost_price'] ?? 0);

        if ($costUsd <= 0) {
            CLI::error('Produto sem custo definido (cost_price = 0)');
            return;
        }

        $productType = $service->detectProductType($product['name'], $category);
        CLI::write("Tipo detectado: {$productType}");
        CLI::newLine();

        $calc = $service->calculateFinalPrice($costUsd, $productType);

        CLI::write('=== CALCULO ===', 'yellow');
        CLI::write("Custo USD: $" . number_format($calc['cost_usd'], 2));
        CLI::write("Taxa cambio: " . $calc['exchange_rate']);
        CLI::write("Custo BRL: R$ " . number_format($calc['cost_brl'], 2, ',', '.'));
        CLI::write("Comissao: " . $calc['commission_rate'] . "%");
        CLI::write("Taxa plataforma: " . $calc['platform_fee'] . "%");
        CLI::write("Margem total: " . $calc['total_margin'] . "%");
        CLI::newLine();

        CLI::write('=== RESULTADO ===', 'cyan');
        CLI::write("Preco ATUAL: R$ " . number_format($product['price'], 2, ',', '.'));
        CLI::write("Preco CALCULADO: R$ " . number_format($calc['final_price_brl'], 2, ',', '.'));
        CLI::write("Lucro estimado: R$ " . number_format($calc['profit_brl'], 2, ',', '.'));

        $diff = $calc['final_price_brl'] - $product['price'];
        if (abs($diff) > 1) {
            CLI::write("Diferenca: R$ " . number_format($diff, 2, ',', '.'), $diff > 0 ? 'red' : 'green');
        }
    }
}
