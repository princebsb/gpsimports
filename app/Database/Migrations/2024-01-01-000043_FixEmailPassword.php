<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixEmailPassword extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $db->table('settings')
            ->where('key', 'smtp_pass')
            ->update(['value' => '@Vendas2026@']);
    }

    public function down()
    {
        // Not reversible
    }
}
