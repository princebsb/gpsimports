<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCashbackTable extends Migration
{
    public function up()
    {
        // Cashback transactions
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
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['earned', 'used', 'expired', 'adjusted'],
                'default' => 'earned',
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'balance_before' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'balance_after' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'available', 'used', 'expired'],
                'default' => 'pending',
            ],
            'available_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('customer_id');
        $this->forge->addKey('order_id');
        $this->forge->addKey('type');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('cashback_transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('cashback_transactions', true);
    }
}
