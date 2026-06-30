<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApiTokensTable extends Migration
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
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'refresh_token' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'device_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'device_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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
            'last_used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'revoked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addKey('token');
        $this->forge->addKey('expires_at');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('api_tokens', true);
    }

    public function down()
    {
        $this->forge->dropTable('api_tokens', true);
    }
}
