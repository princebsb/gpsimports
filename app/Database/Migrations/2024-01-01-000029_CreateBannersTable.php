<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBannersTable extends Migration
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
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'subtitle' => [
                'type' => 'VARCHAR',
                'constraint' => 300,
                'null' => true,
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'image_mobile' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'button_text' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'position' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'home_slider',
            ],
            'text_color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#ffffff',
            ],
            'text_position' => [
                'type' => 'ENUM',
                'constraint' => ['left', 'center', 'right'],
                'default' => 'center',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'starts_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'clicks' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'views' => [
                'type' => 'INT',
                'constraint' => 11,
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
        $this->forge->addKey('position');
        $this->forge->addKey('status');
        $this->forge->addKey('sort_order');

        $this->forge->createTable('banners', true);
    }

    public function down()
    {
        $this->forge->dropTable('banners', true);
    }
}
