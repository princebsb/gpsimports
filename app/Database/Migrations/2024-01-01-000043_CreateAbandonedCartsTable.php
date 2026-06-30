<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbandonedCartsTable extends Migration
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
            'cart_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'items_count' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'recovery_token' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'emails_sent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'last_email_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'recovered' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'recovered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'abandoned_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('customer_id');
        $this->forge->addKey('recovery_token');
        $this->forge->addKey('recovered');

        $this->forge->createTable('abandoned_carts', true);
    }

    public function down()
    {
        $this->forge->dropTable('abandoned_carts', true);
    }
}
