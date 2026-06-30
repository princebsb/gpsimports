<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'user_type' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'customer'],
                'default' => 'admin',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'is_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('user_type');
        $this->forge->addKey('is_read');
        $this->forge->addKey('created_at');

        $this->forge->createTable('notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
    }
}
