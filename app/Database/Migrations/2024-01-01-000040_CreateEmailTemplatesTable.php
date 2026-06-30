<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailTemplatesTable extends Migration
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
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'body' => [
                'type' => 'LONGTEXT',
            ],
            'variables' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'is_system' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
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
        $this->forge->addUniqueKey('code');

        $this->forge->createTable('email_templates', true);

        // Email queue
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'to_email' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'to_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'body' => [
                'type' => 'LONGTEXT',
            ],
            'template_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'priority' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 5,
            ],
            'attempts' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'max_attempts' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 3,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed'],
                'default' => 'pending',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('scheduled_at');

        $this->forge->createTable('email_queue', true);
    }

    public function down()
    {
        $this->forge->dropTable('email_queue', true);
        $this->forge->dropTable('email_templates', true);
    }
}
