<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CouponsSeeder extends Seeder
{
    public function run()
    {
        $coupons = [
            [
                'code' => 'BEMVINDO10',
                'type' => 'percentage',
                'value' => 10.00,
                'min_order_value' => 100.00,
                'max_discount' => 50.00,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
                'starts_at' => date('Y-m-d'),
                'expires_at' => date('Y-12-31'),
                'status' => 'active',
                'description' => 'Cupom de boas-vindas: 10% de desconto na primeira compra',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'FRETEGRATIS',
                'type' => 'free_shipping',
                'value' => 0,
                'min_order_value' => 200.00,
                'max_discount' => null,
                'usage_limit' => 500,
                'usage_limit_per_user' => 3,
                'usage_count' => 0,
                'starts_at' => date('Y-m-d'),
                'expires_at' => date('Y-12-31'),
                'status' => 'active',
                'description' => 'Frete gratis em compras acima de R$ 200',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'DESC50',
                'type' => 'fixed',
                'value' => 50.00,
                'min_order_value' => 300.00,
                'max_discount' => null,
                'usage_limit' => 200,
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
                'starts_at' => date('Y-m-d'),
                'expires_at' => date('Y-m-d', strtotime('+30 days')),
                'status' => 'active',
                'description' => 'R$ 50 de desconto em compras acima de R$ 300',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'BLACKFRIDAY',
                'type' => 'percentage',
                'value' => 20.00,
                'min_order_value' => 500.00,
                'max_discount' => 200.00,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
                'starts_at' => date('Y-11-20'),
                'expires_at' => date('Y-11-30'),
                'status' => 'active',
                'description' => 'Black Friday: 20% de desconto (max R$ 200)',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'NATAL15',
                'type' => 'percentage',
                'value' => 15.00,
                'min_order_value' => 150.00,
                'max_discount' => 100.00,
                'usage_limit' => 800,
                'usage_limit_per_user' => 2,
                'usage_count' => 0,
                'starts_at' => date('Y-12-01'),
                'expires_at' => date('Y-12-25'),
                'status' => 'active',
                'description' => 'Promocao de Natal: 15% de desconto',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('coupons')->truncate();

        foreach ($coupons as $coupon) {
            $this->db->table('coupons')->insert($coupon);
        }

        echo "Coupons seeded successfully.\n";
    }
}
