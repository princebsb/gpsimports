<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductReviewsTable extends Migration
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
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'rating' => [
                'type' => 'TINYINT',
                'constraint' => 1,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'comment' => [
                'type' => 'TEXT',
            ],
            'pros' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'cons' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'images' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'is_verified_purchase' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'helpful_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'admin_reply' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'admin_reply_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
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
        $this->forge->addKey('rating');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('product_reviews', true);
    }

    public function down()
    {
        $this->forge->dropTable('product_reviews', true);
    }
}
