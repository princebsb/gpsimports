<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
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
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'order_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'],
                'default' => 'pending',
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'processing', 'approved', 'rejected', 'refunded', 'chargeback'],
                'default' => 'pending',
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
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
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'items_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'coupon_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'coupon_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'coupon_discount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'cashback_used' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'cashback_earned' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'payment_gateway' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'installments' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 1,
            ],
            'shipping_method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'shipping_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'shipping_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'shipping_zipcode' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
            ],
            'shipping_street' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'shipping_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'shipping_complement' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'shipping_neighborhood' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'shipping_city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'shipping_state' => [
                'type' => 'CHAR',
                'constraint' => 2,
            ],
            'billing_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'billing_cpf' => [
                'type' => 'VARCHAR',
                'constraint' => 14,
                'null' => true,
            ],
            'billing_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'tracking_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tracking_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'estimated_delivery' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'shipped_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'delivered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'admin_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_number');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('status');
        $this->forge->addKey('payment_status');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('orders', true);
    }

    public function down()
    {
        $this->forge->dropTable('orders', true);
    }
}
