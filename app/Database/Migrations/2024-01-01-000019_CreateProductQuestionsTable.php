<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductQuestionsTable extends Migration
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
            'product_id' => [
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
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'customer_email' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'question' => [
                'type' => 'TEXT',
            ],
            'answer' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'answered_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'answered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'helpful_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'answered', 'rejected'],
                'default' => 'pending',
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
        $this->forge->addKey('product_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('product_questions', true);
    }

    public function down()
    {
        $this->forge->dropTable('product_questions', true);
    }
}
