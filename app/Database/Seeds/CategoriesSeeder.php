<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Main Categories
            ['id' => 1, 'name' => 'Eletronicos', 'slug' => 'eletronicos', 'parent_id' => null, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 2, 'name' => 'Informatica', 'slug' => 'informatica', 'parent_id' => null, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 3, 'name' => 'Games', 'slug' => 'games', 'parent_id' => null, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],
            ['id' => 4, 'name' => 'Audio', 'slug' => 'audio', 'parent_id' => null, 'sort_order' => 4, 'status' => 'active', 'is_menu' => 1],
            ['id' => 5, 'name' => 'Acessorios', 'slug' => 'acessorios', 'parent_id' => null, 'sort_order' => 5, 'status' => 'active', 'is_menu' => 1],
            ['id' => 6, 'name' => 'Casa Inteligente', 'slug' => 'casa-inteligente', 'parent_id' => null, 'sort_order' => 6, 'status' => 'active', 'is_menu' => 1],
            ['id' => 7, 'name' => 'Wearables', 'slug' => 'wearables', 'parent_id' => null, 'sort_order' => 7, 'status' => 'active', 'is_menu' => 1],
            ['id' => 8, 'name' => 'Fotografia', 'slug' => 'fotografia', 'parent_id' => null, 'sort_order' => 8, 'status' => 'active', 'is_menu' => 1],

            // Eletronicos Subcategories
            ['id' => 10, 'name' => 'Smartphones', 'slug' => 'smartphones', 'parent_id' => 1, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 11, 'name' => 'Tablets', 'slug' => 'tablets', 'parent_id' => 1, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 12, 'name' => 'TVs', 'slug' => 'tvs', 'parent_id' => 1, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],
            ['id' => 13, 'name' => 'Projetores', 'slug' => 'projetores', 'parent_id' => 1, 'sort_order' => 4, 'status' => 'active', 'is_menu' => 1],

            // Informatica Subcategories
            ['id' => 20, 'name' => 'Notebooks', 'slug' => 'notebooks', 'parent_id' => 2, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 21, 'name' => 'Monitores', 'slug' => 'monitores', 'parent_id' => 2, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 22, 'name' => 'Teclados', 'slug' => 'teclados', 'parent_id' => 2, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],
            ['id' => 23, 'name' => 'Mouses', 'slug' => 'mouses', 'parent_id' => 2, 'sort_order' => 4, 'status' => 'active', 'is_menu' => 1],
            ['id' => 24, 'name' => 'Webcams', 'slug' => 'webcams', 'parent_id' => 2, 'sort_order' => 5, 'status' => 'active', 'is_menu' => 1],
            ['id' => 25, 'name' => 'HDs e SSDs', 'slug' => 'hds-ssds', 'parent_id' => 2, 'sort_order' => 6, 'status' => 'active', 'is_menu' => 1],

            // Games Subcategories
            ['id' => 30, 'name' => 'Consoles', 'slug' => 'consoles', 'parent_id' => 3, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 31, 'name' => 'Controles', 'slug' => 'controles', 'parent_id' => 3, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 32, 'name' => 'Jogos', 'slug' => 'jogos', 'parent_id' => 3, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],
            ['id' => 33, 'name' => 'Headsets Gamer', 'slug' => 'headsets-gamer', 'parent_id' => 3, 'sort_order' => 4, 'status' => 'active', 'is_menu' => 1],
            ['id' => 34, 'name' => 'Cadeiras Gamer', 'slug' => 'cadeiras-gamer', 'parent_id' => 3, 'sort_order' => 5, 'status' => 'active', 'is_menu' => 1],

            // Audio Subcategories
            ['id' => 40, 'name' => 'Fones de Ouvido', 'slug' => 'fones-de-ouvido', 'parent_id' => 4, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 41, 'name' => 'Caixas de Som', 'slug' => 'caixas-de-som', 'parent_id' => 4, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 42, 'name' => 'Soundbars', 'slug' => 'soundbars', 'parent_id' => 4, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],
            ['id' => 43, 'name' => 'Microfones', 'slug' => 'microfones', 'parent_id' => 4, 'sort_order' => 4, 'status' => 'active', 'is_menu' => 1],

            // Acessorios Subcategories
            ['id' => 50, 'name' => 'Capas e Cases', 'slug' => 'capas-cases', 'parent_id' => 5, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 51, 'name' => 'Carregadores', 'slug' => 'carregadores', 'parent_id' => 5, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 52, 'name' => 'Cabos', 'slug' => 'cabos', 'parent_id' => 5, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],
            ['id' => 53, 'name' => 'Power Banks', 'slug' => 'power-banks', 'parent_id' => 5, 'sort_order' => 4, 'status' => 'active', 'is_menu' => 1],
            ['id' => 54, 'name' => 'Suportes', 'slug' => 'suportes', 'parent_id' => 5, 'sort_order' => 5, 'status' => 'active', 'is_menu' => 1],

            // Casa Inteligente Subcategories
            ['id' => 60, 'name' => 'Lampadas Inteligentes', 'slug' => 'lampadas-inteligentes', 'parent_id' => 6, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 61, 'name' => 'Tomadas Inteligentes', 'slug' => 'tomadas-inteligentes', 'parent_id' => 6, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 62, 'name' => 'Cameras de Seguranca', 'slug' => 'cameras-seguranca', 'parent_id' => 6, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],
            ['id' => 63, 'name' => 'Assistentes Virtuais', 'slug' => 'assistentes-virtuais', 'parent_id' => 6, 'sort_order' => 4, 'status' => 'active', 'is_menu' => 1],

            // Wearables Subcategories
            ['id' => 70, 'name' => 'Smartwatches', 'slug' => 'smartwatches', 'parent_id' => 7, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 71, 'name' => 'Pulseiras Fitness', 'slug' => 'pulseiras-fitness', 'parent_id' => 7, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 72, 'name' => 'Oculos VR', 'slug' => 'oculos-vr', 'parent_id' => 7, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],

            // Fotografia Subcategories
            ['id' => 80, 'name' => 'Cameras', 'slug' => 'cameras', 'parent_id' => 8, 'sort_order' => 1, 'status' => 'active', 'is_menu' => 1],
            ['id' => 81, 'name' => 'Drones', 'slug' => 'drones', 'parent_id' => 8, 'sort_order' => 2, 'status' => 'active', 'is_menu' => 1],
            ['id' => 82, 'name' => 'Action Cameras', 'slug' => 'action-cameras', 'parent_id' => 8, 'sort_order' => 3, 'status' => 'active', 'is_menu' => 1],
            ['id' => 83, 'name' => 'Lentes', 'slug' => 'lentes', 'parent_id' => 8, 'sort_order' => 4, 'status' => 'active', 'is_menu' => 1],
            ['id' => 84, 'name' => 'Tripes', 'slug' => 'tripes', 'parent_id' => 8, 'sort_order' => 5, 'status' => 'active', 'is_menu' => 1],
        ];

        $this->db->table('categories')->truncate();

        foreach ($categories as $category) {
            $category['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('categories')->insert($category);
        }

        echo "Categories seeded successfully.\n";
    }
}
