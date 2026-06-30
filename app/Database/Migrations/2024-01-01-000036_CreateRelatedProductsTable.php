<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRelatedProductsTable extends Migration
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
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'related_product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['related', 'upsell', 'crosssell'],
                'default' => 'related',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['product_id', 'related_product_id', 'type']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('related_product_id', 'products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('related_products', true);
    }

    public function down()
    {
        $this->forge->dropTable('related_products', true);
    }
}
