<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductAttributesTable extends Migration
{
    public function up()
    {
        // Attribute types
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['select', 'color', 'size', 'text'],
                'default' => 'select',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');

        $this->forge->createTable('attributes', true);

        // Attribute values
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'attribute_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'color_code' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('attribute_id');
        $this->forge->addForeignKey('attribute_id', 'attributes', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('attribute_values', true);

        // Product attributes relation
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
            'attribute_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'attribute_value_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'custom_value' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['product_id', 'attribute_id']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('attribute_id', 'attributes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('attribute_value_id', 'attribute_values', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('product_attributes', true);
    }

    public function down()
    {
        $this->forge->dropTable('product_attributes', true);
        $this->forge->dropTable('attribute_values', true);
        $this->forge->dropTable('attributes', true);
    }
}
