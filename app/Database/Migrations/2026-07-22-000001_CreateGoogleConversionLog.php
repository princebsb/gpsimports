<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGoogleConversionLog extends Migration
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
            'transaction_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'purchase',
            ],
            'sent_at' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed'],
                'default' => 'sent',
            ],
            'response' => [
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addKey('transaction_id');
        $this->forge->addKey(['order_id', 'status']);

        $this->forge->createTable('google_conversion_log');
    }

    public function down()
    {
        $this->forge->dropTable('google_conversion_log');
    }
}
