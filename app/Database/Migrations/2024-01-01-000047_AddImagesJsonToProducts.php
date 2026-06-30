<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagesJsonToProducts extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('images_json', 'products')) {
            $this->forge->addColumn('products', [
                'images_json' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'featured_image',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('images_json', 'products')) {
            $this->forge->dropColumn('products', 'images_json');
        }
    }
}
