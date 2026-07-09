<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigureEmailSettings extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $settings = [
            'smtp_host' => 'smtp.hostinger.com',
            'smtp_port' => '465',
            'smtp_crypto' => 'ssl',
            'smtp_user' => 'vendas@gpsimports.com.br',
            'smtp_pass' => '@Vendas2026@',
            'store_email' => 'vendas@gpsimports.com.br',
            'mail_from_name' => 'GPS Imports',
            'mail_from_email' => 'vendas@gpsimports.com.br',
        ];

        foreach ($settings as $key => $value) {
            $existing = $db->table('settings')->where('key', $key)->get()->getRow();

            if ($existing) {
                $db->table('settings')->where('key', $key)->update(['value' => $value]);
            } else {
                $type = 'text';
                if ($key === 'smtp_pass') $type = 'password';
                if ($key === 'smtp_port') $type = 'number';
                if (strpos($key, 'email') !== false) $type = 'email';

                $db->table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'type' => $type,
                ]);
            }
        }
    }

    public function down()
    {
        // Not reversible - would need to restore original values
    }
}
