<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
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
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'brand_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'sku' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'barcode' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
            ],
            'short_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'sale_price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'cost_price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'sale_start' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'sale_end' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'weight' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'default' => 0.000,
            ],
            'width' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'height' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'length' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'stock_min' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'manage_stock' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'allow_backorder' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'has_variations' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'meta_title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'meta_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'meta_keywords' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'tags' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'featured_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'video_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_new' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_bestseller' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'views' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'sales_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'rating_average' => [
                'type' => 'DECIMAL',
                'constraint' => '3,2',
                'default' => 0.00,
            ],
            'rating_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'draft'],
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('sku');
        $this->forge->addKey('category_id');
        $this->forge->addKey('brand_id');
        $this->forge->addKey('status');
        $this->forge->addKey('is_featured');
        $this->forge->addKey('price');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('brand_id', 'brands', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('products', true);

        // Add fulltext index for search
        $this->db->query('ALTER TABLE `' . $this->db->prefixTable('products') . '` ADD FULLTEXT INDEX `ft_products_search` (`name`, `short_description`, `tags`)');
    }

    public function down()
    {
        $this->forge->dropTable('products', true);
    }
}
