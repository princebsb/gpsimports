<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPixDiscountToOrders extends Migration
{
    public function up()
    {
        // Verifica se a coluna ja existe
        if (!$this->db->fieldExists('pix_discount', 'orders')) {
            $this->forge->addColumn('orders', [
                'pix_discount' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'default' => 0.00,
                    'after' => 'coupon_discount',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('pix_discount', 'orders')) {
            $this->forge->dropColumn('orders', 'pix_discount');
        }
    }
}
