<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Run seeders in order
        $this->call('SettingsSeeder');
        $this->call('RolesSeeder');
        $this->call('UsersSeeder');
        $this->call('CategoriesSeeder');
        $this->call('BrandsSeeder');
        $this->call('ProductsSeeder');
        $this->call('CustomersSeeder');
        $this->call('CouponsSeeder');
        $this->call('BannersSeeder');
        $this->call('ShippingSeeder');
        $this->call('PaymentMethodsSeeder');
    }
}
