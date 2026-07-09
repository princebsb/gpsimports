<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixSmtpSSL extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Hostinger uses port 465 with SSL
        $db->table('settings')->where('key', 'smtp_port')->update(['value' => '465']);
        $db->table('settings')->where('key', 'smtp_crypto')->update(['value' => 'ssl']);

        // Clear settings cache
        cache()->delete('settings');
    }

    public function down()
    {
        // Not reversible
    }
}
