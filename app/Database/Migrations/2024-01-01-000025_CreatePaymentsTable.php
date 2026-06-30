<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
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
            'gateway' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'transaction_id' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'external_id' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'processing', 'approved', 'rejected', 'refunded', 'cancelled', 'chargeback'],
                'default' => 'pending',
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'fee' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'net_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'installments' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 1,
            ],
            'installment_value' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'card_brand' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'card_last_digits' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
                'null' => true,
            ],
            'card_holder_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'pix_qrcode' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pix_qrcode_base64' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'pix_copy_paste' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pix_expiration' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'boleto_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'boleto_barcode' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'boleto_expiration' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'gateway_response' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'refunded_at' => [
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
        $this->forge->addKey('order_id');
        $this->forge->addKey('transaction_id');
        $this->forge->addKey('external_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('payments', true);
    }

    public function down()
    {
        $this->forge->dropTable('payments', true);
    }
}
