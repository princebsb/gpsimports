<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockTable extends Migration
{
    public function up()
    {
        // Stock movements
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'variation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['in', 'out', 'adjustment', 'return', 'reserved', 'released'],
                'default' => 'in',
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'previous_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'current_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'reference_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'reference_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id');
        $this->forge->addKey('variation_id');
        $this->forge->addKey('type');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('stock_movements', true);

        // Stock alerts
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
            'variation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['low_stock', 'out_of_stock', 'overstock'],
                'default' => 'low_stock',
            ],
            'current_stock' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'threshold' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'is_resolved' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'resolved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id');
        $this->forge->addKey('type');
        $this->forge->addKey('is_resolved');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('stock_alerts', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_alerts', true);
        $this->forge->dropTable('stock_movements', true);
    }
}
