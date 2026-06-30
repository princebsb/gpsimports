<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSeoUrlsTable extends Migration
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
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
            ],
            'redirect_to' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'redirect_type' => [
                'type' => 'ENUM',
                'constraint' => ['301', '302'],
                'default' => '301',
            ],
            'hits' => [
                'type' => 'INT',
                'constraint' => 11,
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
        $this->forge->addKey('url');
        $this->forge->addKey('status');

        $this->forge->createTable('seo_redirects', true);
    }

    public function down()
    {
        $this->forge->dropTable('seo_redirects', true);
    }
}
