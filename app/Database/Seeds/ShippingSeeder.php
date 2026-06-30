<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run()
    {
        // Shipping Methods
        $shippingMethods = [
            [
                'name' => 'PAC',
                'code' => 'pac',
                'provider' => 'correios',
                'description' => 'PAC - Encomenda Economica',
                'status' => 'active',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'SEDEX',
                'code' => 'sedex',
                'provider' => 'correios',
                'description' => 'SEDEX - Envio Expresso',
                'status' => 'active',
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'SEDEX 10',
                'code' => 'sedex10',
                'provider' => 'correios',
                'description' => 'SEDEX 10 - Entrega ate as 10h',
                'status' => 'active',
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Retirar na Loja',
                'code' => 'pickup',
                'provider' => 'local',
                'description' => 'Retire gratuitamente em nossa loja',
                'status' => 'active',
                'sort_order' => 4,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Shipping Zones
        $shippingZones = [
            [
                'name' => 'Sao Paulo - Capital',
                'states' => json_encode(['SP']),
                'zipcodes' => json_encode(['01000-000', '05999-999']),
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Grande Sao Paulo',
                'states' => json_encode(['SP']),
                'zipcodes' => json_encode(['01000-000', '09999-999']),
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Sudeste',
                'states' => json_encode(['SP', 'RJ', 'MG', 'ES']),
                'zipcodes' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Sul',
                'states' => json_encode(['PR', 'SC', 'RS']),
                'zipcodes' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Brasil',
                'states' => null,
                'zipcodes' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('shipping_methods')->truncate();
        $this->db->table('shipping_zones')->truncate();
        $this->db->table('shipping_rates')->truncate();

        foreach ($shippingMethods as $method) {
            $this->db->table('shipping_methods')->insert($method);
        }

        foreach ($shippingZones as $zone) {
            $this->db->table('shipping_zones')->insert($zone);
        }

        echo "Shipping seeded successfully.\n";
    }
}
