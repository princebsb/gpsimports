<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixSmtpPort extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Update port to 587
        $db->table('settings')
            ->where('key', 'smtp_port')
            ->update(['value' => '587']);

        // Update crypto to TLS (port 587 uses STARTTLS)
        $db->table('settings')
            ->where('key', 'smtp_crypto')
            ->update(['value' => 'tls']);
    }

    public function down()
    {
        // Not reversible
    }
}
