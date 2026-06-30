<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class CheckProductsCommand extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'products:check';
    protected $description = 'Verifica dados dos produtos';

    public function run(array $params)
    {
        $db = Database::connect();

        $total = $db->table('products')->countAllResults();
        $withCost = $db->table('products')->where('cost_price >', 0)->countAllResults();
        $withPrice = $db->table('products')->where('price >', 0)->countAllResults();

        CLI::write("Total de produtos: {$total}");
        CLI::write("Com cost_price > 0: {$withCost}");
        CLI::write("Com price > 0: {$withPrice}");

        CLI::newLine();
        CLI::write('Amostra de produtos:', 'green');

        $products = $db->table('products')
            ->select('id, name, price, cost_price, category_id')
            ->limit(10)
            ->get()
            ->getResultArray();

        foreach ($products as $p) {
            CLI::write(sprintf('#%d | %s | Preco: R$ %.2f | Custo: $%.2f',
                $p['id'],
                substr($p['name'], 0, 50),
                $p['price'],
                $p['cost_price'] ?? 0
            ));
        }
    }
}
