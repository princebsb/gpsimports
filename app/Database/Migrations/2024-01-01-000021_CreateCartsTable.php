<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCartsTable extends Migration
{
    public function up()
    {
        // Carts
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'coupon_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'discount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'shipping_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'shipping_method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'shipping_zipcode' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
                'null' => true,
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'items_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('customer_id');
        $this->forge->addKey('session_id');
        $this->forge->addKey('expires_at');

        $this->forge->createTable('carts', true);

        // Cart items
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'cart_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 300,
            ],
            'sku' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'attributes' => [
                'type' => 'JSON',
                'null' => true,
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
        $this->forge->addKey('cart_id');
        $this->forge->addKey('product_id');
        $this->forge->addForeignKey('cart_id', 'carts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('cart_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('cart_items', true);
        $this->forge->dropTable('carts', true);
    }
}
