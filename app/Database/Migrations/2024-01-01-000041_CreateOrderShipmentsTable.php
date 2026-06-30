<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderShipmentsTable extends Migration
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
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'carrier' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'service' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tracking_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'tracking_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'weight' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
            ],
            'cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'label_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'posted', 'in_transit', 'out_for_delivery', 'delivered', 'returned', 'failed'],
                'default' => 'pending',
            ],
            'estimated_delivery' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'shipped_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'delivered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('order_id');
        $this->forge->addKey('tracking_code');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('order_shipments', true);

        // Shipment tracking events
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'shipment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'occurred_at' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('shipment_id');
        $this->forge->addForeignKey('shipment_id', 'order_shipments', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('shipment_tracking', true);
    }

    public function down()
    {
        $this->forge->dropTable('shipment_tracking', true);
        $this->forge->dropTable('order_shipments', true);
    }
}
