<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSearchHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'term' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'results_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('term');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('created_at');

        $this->forge->createTable('search_history', true);

        // Popular searches (aggregated)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'term' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'search_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'last_searched_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('term');
        $this->forge->addKey('search_count');

        $this->forge->createTable('popular_searches', true);
    }

    public function down()
    {
        $this->forge->dropTable('popular_searches', true);
        $this->forge->dropTable('search_history', true);
    }
}
