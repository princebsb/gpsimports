<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BrandsSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            ['id' => 1, 'name' => 'Apple', 'slug' => 'apple', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 1],
            ['id' => 2, 'name' => 'Samsung', 'slug' => 'samsung', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 2],
            ['id' => 3, 'name' => 'Xiaomi', 'slug' => 'xiaomi', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 3],
            ['id' => 4, 'name' => 'Sony', 'slug' => 'sony', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 4],
            ['id' => 5, 'name' => 'LG', 'slug' => 'lg', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 5],
            ['id' => 6, 'name' => 'JBL', 'slug' => 'jbl', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 6],
            ['id' => 7, 'name' => 'Logitech', 'slug' => 'logitech', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 7],
            ['id' => 8, 'name' => 'Razer', 'slug' => 'razer', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 8],
            ['id' => 9, 'name' => 'Microsoft', 'slug' => 'microsoft', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 9],
            ['id' => 10, 'name' => 'Google', 'slug' => 'google', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 10],
            ['id' => 11, 'name' => 'Bose', 'slug' => 'bose', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 11],
            ['id' => 12, 'name' => 'Anker', 'slug' => 'anker', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 12],
            ['id' => 13, 'name' => 'DJI', 'slug' => 'dji', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 13],
            ['id' => 14, 'name' => 'GoPro', 'slug' => 'gopro', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 14],
            ['id' => 15, 'name' => 'Canon', 'slug' => 'canon', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 15],
            ['id' => 16, 'name' => 'Nikon', 'slug' => 'nikon', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 16],
            ['id' => 17, 'name' => 'Huawei', 'slug' => 'huawei', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 17],
            ['id' => 18, 'name' => 'Motorola', 'slug' => 'motorola', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 18],
            ['id' => 19, 'name' => 'ASUS', 'slug' => 'asus', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 19],
            ['id' => 20, 'name' => 'Dell', 'slug' => 'dell', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 20],
            ['id' => 21, 'name' => 'HP', 'slug' => 'hp', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 21],
            ['id' => 22, 'name' => 'Lenovo', 'slug' => 'lenovo', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 22],
            ['id' => 23, 'name' => 'Nintendo', 'slug' => 'nintendo', 'status' => 'active', 'is_featured' => 1, 'sort_order' => 23],
            ['id' => 24, 'name' => 'HyperX', 'slug' => 'hyperx', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 24],
            ['id' => 25, 'name' => 'SteelSeries', 'slug' => 'steelseries', 'status' => 'active', 'is_featured' => 0, 'sort_order' => 25],
        ];

        $this->db->table('brands')->truncate();

        foreach ($brands as $brand) {
            $brand['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('brands')->insert($brand);
        }

        echo "Brands seeded successfully.\n";
    }
}
