<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderStatusHistoryTable extends Migration
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
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'comment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'notify_customer' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('order_status_history', true);
    }

    public function down()
    {
        $this->forge->dropTable('order_status_history', true);
    }
}
