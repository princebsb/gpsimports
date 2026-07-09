<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixEmailCredentials extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $settings = [
            'smtp_host' => 'smtp.hostinger.com',
            'smtp_port' => '587',
            'smtp_crypto' => 'tls',
            'smtp_user' => 'vendas@gpsimports.com.br',
            'smtp_pass' => '@Vendas2026@',
            'store_email' => 'vendas@gpsimports.com.br',
            'mail_from_name' => 'GPS Imports',
            'mail_from_email' => 'vendas@gpsimports.com.br',
            'email_from_name' => 'GPS Imports',
            'email_from_address' => 'vendas@gpsimports.com.br',
        ];

        foreach ($settings as $key => $value) {
            $existing = $db->table('settings')->where('key', $key)->get()->getRow();

            if ($existing) {
                $db->table('settings')->where('key', $key)->update(['value' => $value]);
            } else {
                $db->table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'type' => 'text',
                ]);
            }
        }

        // Clear settings cache
        cache()->delete('settings');
    }

    public function down()
    {
        // Not reversible
    }
}
