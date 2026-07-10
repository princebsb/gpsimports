<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderEmailsTable extends Migration
{
    public function up()
    {
        // Tabela para rastrear emails enviados de pedidos
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
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'payment_reminder, order_cancelled, order_shipped, etc',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addKey(['order_id', 'type']);
        $this->forge->createTable('order_emails', true);

        // Tabela para rastrear carrinhos abandonados
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
                'constraint' => 255,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'items' => [
                'type' => 'TEXT',
                'comment' => 'JSON com itens do carrinho',
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'email_sent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'email_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'recovered' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 se cliente finalizou compra',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('customer_id');
        $this->forge->addKey('session_id');
        $this->forge->addKey('email_sent');
        $this->forge->createTable('cart_abandonment', true);
    }

    public function down()
    {
        $this->forge->dropTable('order_emails', true);
        $this->forge->dropTable('cart_abandonment', true);
    }
}
