<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerAddressesTable extends Migration
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
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'recipient' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'zipcode' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
            ],
            'street' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'complement' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'neighborhood' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'state' => [
                'type' => 'CHAR',
                'constraint' => 2,
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Brasil',
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'is_default' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['shipping', 'billing', 'both'],
                'default' => 'both',
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
        $this->forge->addKey('customer_id');
        $this->forge->addKey('zipcode');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('customer_addresses', true);
    }

    public function down()
    {
        $this->forge->dropTable('customer_addresses', true);
    }
}
