<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // Store Info
            ['key' => 'store_name', 'value' => 'GPS Imports', 'type' => 'text'],
            ['key' => 'store_slogan', 'value' => 'Os melhores produtos importados', 'type' => 'text'],
            ['key' => 'store_description', 'value' => 'Loja online especializada em produtos importados de alta qualidade.', 'type' => 'textarea'],
            ['key' => 'about_short', 'value' => 'Somos uma loja online especializada em trazer os melhores produtos importados para voce, com qualidade e preco justo.', 'type' => 'textarea'],

            // Contact
            ['key' => 'email', 'value' => 'contato@gpsimports.com.br', 'type' => 'email'],
            ['key' => 'phone', 'value' => '(11) 99999-9999', 'type' => 'text'],
            ['key' => 'whatsapp', 'value' => '5511999999999', 'type' => 'text'],
            ['key' => 'address', 'value' => 'Rua Exemplo, 123 - Centro, Sao Paulo - SP, 01000-000', 'type' => 'text'],

            // Social Media
            ['key' => 'facebook', 'value' => 'https://facebook.com/gpsimports', 'type' => 'url'],
            ['key' => 'instagram', 'value' => 'https://instagram.com/gpsimports', 'type' => 'url'],
            ['key' => 'youtube', 'value' => '', 'type' => 'url'],
            ['key' => 'twitter', 'value' => '', 'type' => 'url'],

            // Appearance
            ['key' => 'primary_color', 'value' => '#2563eb', 'type' => 'color'],
            ['key' => 'secondary_color', 'value' => '#1e293b', 'type' => 'color'],
            ['key' => 'logo', 'value' => '', 'type' => 'image'],
            ['key' => 'favicon', 'value' => '', 'type' => 'image'],

            // SEO
            ['key' => 'meta_title', 'value' => 'GPS Imports - Os Melhores Produtos Importados', 'type' => 'text'],
            ['key' => 'meta_description', 'value' => 'Encontre os melhores produtos importados com qualidade e preco justo. Eletronicos, acessorios, games e muito mais.', 'type' => 'textarea'],
            ['key' => 'meta_keywords', 'value' => 'importados, eletronicos, acessorios, games, loja online', 'type' => 'text'],
            ['key' => 'google_analytics', 'value' => '', 'type' => 'text'],
            ['key' => 'google_tag_manager', 'value' => '', 'type' => 'text'],

            // Shipping
            ['key' => 'free_shipping_min', 'value' => '299', 'type' => 'number'],
            ['key' => 'origin_zipcode', 'value' => '01310100', 'type' => 'text'],

            // Payment
            ['key' => 'pix_discount', 'value' => '5', 'type' => 'number'],
            ['key' => 'max_installments', 'value' => '12', 'type' => 'number'],
            ['key' => 'min_installment_value', 'value' => '10', 'type' => 'number'],

            // Stock
            ['key' => 'low_stock_alert', 'value' => '5', 'type' => 'number'],
            ['key' => 'out_of_stock_visibility', 'value' => '1', 'type' => 'boolean'],

            // Email
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com', 'type' => 'text'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'number'],
            ['key' => 'smtp_user', 'value' => '', 'type' => 'text'],
            ['key' => 'smtp_pass', 'value' => '', 'type' => 'password'],
            ['key' => 'mail_from_name', 'value' => 'GPS Imports', 'type' => 'text'],
            ['key' => 'mail_from_email', 'value' => 'noreply@gpsimports.com.br', 'type' => 'email'],

            // Legal
            ['key' => 'cnpj', 'value' => '12.345.678/0001-90', 'type' => 'text'],
            ['key' => 'razao_social', 'value' => 'GPS Imports Comercio LTDA', 'type' => 'text'],
        ];

        $this->db->table('settings')->truncate();

        foreach ($settings as $setting) {
            $this->db->table('settings')->insert($setting);
        }

        echo "Settings seeded successfully.\n";
    }
}
