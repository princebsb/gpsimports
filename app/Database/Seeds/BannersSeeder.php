<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BannersSeeder extends Seeder
{
    public function run()
    {
        $banners = [
            [
                'title' => 'iPhone 15 Pro Max',
                'subtitle' => 'O iPhone mais poderoso de todos os tempos',
                'image' => 'banner-iphone15.jpg',
                'link' => '/produto/iphone-15-pro-max-256gb',
                'position' => 'home_slider',
                'sort_order' => 1,
                'status' => 'active',
                'starts_at' => date('Y-m-d'),
                'expires_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'PlayStation 5',
                'subtitle' => 'A nova geracao de games',
                'image' => 'banner-ps5.jpg',
                'link' => '/produto/playstation-5-standard',
                'position' => 'home_slider',
                'sort_order' => 2,
                'status' => 'active',
                'starts_at' => date('Y-m-d'),
                'expires_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'MacBook Pro M3',
                'subtitle' => 'Performance extraordinaria',
                'image' => 'banner-macbook.jpg',
                'link' => '/produto/macbook-pro-14-m3-pro-512gb',
                'position' => 'home_slider',
                'sort_order' => 3,
                'status' => 'active',
                'starts_at' => date('Y-m-d'),
                'expires_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Frete Gratis',
                'subtitle' => 'Em compras acima de R$ 299',
                'image' => 'banner-frete.jpg',
                'link' => '/produtos',
                'position' => 'home_slider',
                'sort_order' => 4,
                'status' => 'active',
                'starts_at' => date('Y-m-d'),
                'expires_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Promocoes de Audio',
                'subtitle' => 'Ate 30% de desconto',
                'image' => 'banner-audio.jpg',
                'link' => '/categoria/audio',
                'position' => 'home_secondary',
                'sort_order' => 1,
                'status' => 'active',
                'starts_at' => date('Y-m-d'),
                'expires_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Acessorios',
                'subtitle' => 'Complemente seu setup',
                'image' => 'banner-acessorios.jpg',
                'link' => '/categoria/acessorios',
                'position' => 'home_secondary',
                'sort_order' => 2,
                'status' => 'active',
                'starts_at' => date('Y-m-d'),
                'expires_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('banners')->truncate();

        foreach ($banners as $banner) {
            $this->db->table('banners')->insert($banner);
        }

        echo "Banners seeded successfully.\n";
    }
}
