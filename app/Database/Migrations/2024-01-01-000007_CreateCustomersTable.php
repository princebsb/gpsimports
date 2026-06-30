<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomersTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'cpf' => [
                'type' => 'VARCHAR',
                'constraint' => 14,
                'null' => true,
            ],
            'cnpj' => [
                'type' => 'VARCHAR',
                'constraint' => 18,
                'null' => true,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'mobile' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'birth_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['M', 'F', 'O'],
                'null' => true,
            ],
            'avatar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'blocked'],
                'default' => 'active',
            ],
            'email_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'newsletter' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'cashback_balance' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'total_orders' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_spent' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'last_order_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'remember_token' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('cpf');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');

        $this->forge->createTable('customers', true);
    }

    public function down()
    {
        $this->forge->dropTable('customers', true);
    }
}
