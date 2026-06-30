<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShippingMethodsTable extends Migration
{
    public function up()
    {
        // Shipping methods
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
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'provider' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'calculation_type' => [
                'type' => 'ENUM',
                'constraint' => ['api', 'fixed', 'table', 'free'],
                'default' => 'api',
            ],
            'fixed_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'fixed_days' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'additional_days' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'additional_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'min_weight' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'max_weight' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'min_order_value' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'max_order_value' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'sort_order' => [
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
        $this->forge->addKey('code');
        $this->forge->addKey('status');

        $this->forge->createTable('shipping_methods', true);

        // Shipping zones
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
            'states' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'zipcodes' => [
                'type' => 'JSON',
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

        $this->forge->createTable('shipping_zones', true);

        // Shipping rates (for table-based calculation)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'shipping_method_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'shipping_zone_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'min_weight' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'default' => 0,
            ],
            'max_weight' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'delivery_days' => [
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
        $this->forge->addKey('shipping_method_id');
        $this->forge->addKey('shipping_zone_id');
        $this->forge->addForeignKey('shipping_method_id', 'shipping_methods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('shipping_zone_id', 'shipping_zones', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('shipping_rates', true);
    }

    public function down()
    {
        $this->forge->dropTable('shipping_rates', true);
        $this->forge->dropTable('shipping_zones', true);
        $this->forge->dropTable('shipping_methods', true);
    }
}
