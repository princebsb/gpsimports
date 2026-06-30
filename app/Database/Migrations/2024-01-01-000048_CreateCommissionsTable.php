<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'product_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Tipo: perfumes, cosmeticos, celulares, eletronicos, apple_iphone17, apple_iphone16, etc',
            ],
            'price_min_usd' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Preco minimo em USD',
            ],
            'price_max_usd' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'Preco maximo em USD (null = sem limite)',
            ],
            'commission_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'comment' => 'Taxa de comissao em percentual (ex: 20.00 = 20%)',
            ],
            'platform_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 15.00,
                'comment' => 'Taxa da plataforma em percentual',
            ],
            'is_blocked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = produto bloqueado para venda nesta faixa',
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['product_type', 'price_min_usd']);
        $this->forge->createTable('commissions');

        // Insert default commission rates based on the image provided (March 2026)
        $this->seedCommissions();
    }

    public function down()
    {
        $this->forge->dropTable('commissions');
    }

    private function seedCommissions()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $commissions = [
            // PERFUMES
            ['product_type' => 'perfumes', 'price_min_usd' => 0.01, 'price_max_usd' => 8.49, 'commission_rate' => 0, 'is_blocked' => 1, 'notes' => 'Bloqueado - NAO vender'],
            ['product_type' => 'perfumes', 'price_min_usd' => 8.50, 'price_max_usd' => 20.99, 'commission_rate' => 33.00, 'notes' => 'Comissao 33%'],
            ['product_type' => 'perfumes', 'price_min_usd' => 21.00, 'price_max_usd' => 119.99, 'commission_rate' => 28.00, 'notes' => 'Comissao 28%'],
            ['product_type' => 'perfumes', 'price_min_usd' => 120.00, 'price_max_usd' => null, 'commission_rate' => 20.00, 'notes' => 'Custo > $120 = 20%'],

            // COSMETICOS (mesma tabela de perfumes)
            ['product_type' => 'cosmeticos', 'price_min_usd' => 0.01, 'price_max_usd' => 8.49, 'commission_rate' => 0, 'is_blocked' => 1, 'notes' => 'Bloqueado - NAO vender'],
            ['product_type' => 'cosmeticos', 'price_min_usd' => 8.50, 'price_max_usd' => 20.99, 'commission_rate' => 33.00, 'notes' => 'Comissao 33%'],
            ['product_type' => 'cosmeticos', 'price_min_usd' => 21.00, 'price_max_usd' => 119.99, 'commission_rate' => 28.00, 'notes' => 'Comissao 28%'],
            ['product_type' => 'cosmeticos', 'price_min_usd' => 120.00, 'price_max_usd' => null, 'commission_rate' => 20.00, 'notes' => 'Custo > $120 = 20%'],

            // CELULARES
            ['product_type' => 'celulares', 'price_min_usd' => 0.01, 'price_max_usd' => 89.99, 'commission_rate' => 25.00, 'notes' => '< $90 = 25%'],
            ['product_type' => 'celulares', 'price_min_usd' => 90.00, 'price_max_usd' => 100.99, 'commission_rate' => 22.00, 'notes' => 'ate $100 = 22%'],
            ['product_type' => 'celulares', 'price_min_usd' => 101.00, 'price_max_usd' => null, 'commission_rate' => 20.00, 'notes' => '$101+ = 20%'],

            // ELETRONICOS
            ['product_type' => 'eletronicos', 'price_min_usd' => 0.01, 'price_max_usd' => null, 'commission_rate' => 20.00, 'notes' => 'Qualquer valor = 20%'],

            // APPLE - iPhone 17
            ['product_type' => 'apple_iphone17', 'price_min_usd' => 0.01, 'price_max_usd' => null, 'commission_rate' => 12.00, 'notes' => 'iPhone 17 = 12%'],

            // APPLE - iPhone 16
            ['product_type' => 'apple_iphone16', 'price_min_usd' => 0.01, 'price_max_usd' => null, 'commission_rate' => 15.00, 'notes' => 'iPhone 16 = 15%'],

            // APPLE - iPad/Airpods
            ['product_type' => 'apple_ipad', 'price_min_usd' => 0.01, 'price_max_usd' => null, 'commission_rate' => 20.00, 'notes' => 'iPad = 20%'],
            ['product_type' => 'apple_airpods', 'price_min_usd' => 0.01, 'price_max_usd' => null, 'commission_rate' => 20.00, 'notes' => 'Airpods = 20%'],

            // APPLE - Macbook
            ['product_type' => 'apple_macbook', 'price_min_usd' => 0.01, 'price_max_usd' => null, 'commission_rate' => 15.00, 'notes' => 'Macbook = 15%'],

            // APPLE - iPhone 15 e anteriores / CPO / SWAP
            ['product_type' => 'apple_iphone_antigo', 'price_min_usd' => 0.01, 'price_max_usd' => null, 'commission_rate' => 20.00, 'notes' => 'iPhone 15 e anteriores / CPO / SWAP = 20%'],

            // Tipo padrao para produtos nao categorizados
            ['product_type' => 'outros', 'price_min_usd' => 0.01, 'price_max_usd' => null, 'commission_rate' => 20.00, 'notes' => 'Padrao para produtos nao categorizados'],
        ];

        foreach ($commissions as $commission) {
            $commission['platform_fee'] = 15.00;
            $commission['active'] = 1;
            $commission['created_at'] = $now;
            $commission['updated_at'] = $now;
            $db->table('commissions')->insert($commission);
        }
    }
}
