<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Roles
        $roles = [
            [
                'id' => 1,
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Acesso total ao sistema',
                'is_system' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Gerente',
                'slug' => 'gerente',
                'description' => 'Gerencia produtos, pedidos e clientes',
                'is_system' => 0,
            ],
            [
                'id' => 3,
                'name' => 'Atendente',
                'slug' => 'atendente',
                'description' => 'Visualiza e atende pedidos',
                'is_system' => 0,
            ],
        ];

        // Permissions
        $permissions = [
            // Dashboard
            ['name' => 'Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],

            // Products
            ['name' => 'Listar Produtos', 'slug' => 'products.view', 'module' => 'products'],
            ['name' => 'Criar Produtos', 'slug' => 'products.create', 'module' => 'products'],
            ['name' => 'Editar Produtos', 'slug' => 'products.edit', 'module' => 'products'],
            ['name' => 'Excluir Produtos', 'slug' => 'products.delete', 'module' => 'products'],

            // Categories
            ['name' => 'Listar Categorias', 'slug' => 'categories.view', 'module' => 'categories'],
            ['name' => 'Gerenciar Categorias', 'slug' => 'categories.manage', 'module' => 'categories'],

            // Brands
            ['name' => 'Listar Marcas', 'slug' => 'brands.view', 'module' => 'brands'],
            ['name' => 'Gerenciar Marcas', 'slug' => 'brands.manage', 'module' => 'brands'],

            // Orders
            ['name' => 'Listar Pedidos', 'slug' => 'orders.view', 'module' => 'orders'],
            ['name' => 'Atualizar Pedidos', 'slug' => 'orders.update', 'module' => 'orders'],
            ['name' => 'Cancelar Pedidos', 'slug' => 'orders.cancel', 'module' => 'orders'],

            // Customers
            ['name' => 'Listar Clientes', 'slug' => 'customers.view', 'module' => 'customers'],
            ['name' => 'Gerenciar Clientes', 'slug' => 'customers.manage', 'module' => 'customers'],

            // Coupons
            ['name' => 'Listar Cupons', 'slug' => 'coupons.view', 'module' => 'coupons'],
            ['name' => 'Gerenciar Cupons', 'slug' => 'coupons.manage', 'module' => 'coupons'],

            // Banners
            ['name' => 'Listar Banners', 'slug' => 'banners.view', 'module' => 'banners'],
            ['name' => 'Gerenciar Banners', 'slug' => 'banners.manage', 'module' => 'banners'],

            // Reports
            ['name' => 'Ver Relatorios', 'slug' => 'reports.view', 'module' => 'reports'],

            // Settings
            ['name' => 'Ver Configuracoes', 'slug' => 'settings.view', 'module' => 'settings'],
            ['name' => 'Gerenciar Configuracoes', 'slug' => 'settings.manage', 'module' => 'settings'],

            // Users
            ['name' => 'Listar Usuarios', 'slug' => 'users.view', 'module' => 'users'],
            ['name' => 'Gerenciar Usuarios', 'slug' => 'users.manage', 'module' => 'users'],
        ];

        $this->db->table('roles')->truncate();
        $this->db->table('permissions')->truncate();
        $this->db->table('role_permissions')->truncate();

        foreach ($roles as $role) {
            $this->db->table('roles')->insert($role);
        }

        foreach ($permissions as $permission) {
            $this->db->table('permissions')->insert($permission);
        }

        // Super Admin gets all permissions
        $allPermissions = $this->db->table('permissions')->get()->getResultArray();
        foreach ($allPermissions as $permission) {
            $this->db->table('role_permissions')->insert([
                'role_id' => 1,
                'permission_id' => $permission['id'],
            ]);
        }

        // Gerente permissions
        $gerentePermissions = ['dashboard.view', 'products.view', 'products.create', 'products.edit', 'categories.view', 'categories.manage', 'brands.view', 'brands.manage', 'orders.view', 'orders.update', 'customers.view', 'coupons.view', 'coupons.manage', 'banners.view', 'banners.manage', 'reports.view'];

        foreach ($gerentePermissions as $slug) {
            $permission = $this->db->table('permissions')->where('slug', $slug)->get()->getRowArray();
            if ($permission) {
                $this->db->table('role_permissions')->insert([
                    'role_id' => 2,
                    'permission_id' => $permission['id'],
                ]);
            }
        }

        // Atendente permissions
        $atendentePermissions = ['dashboard.view', 'orders.view', 'orders.update', 'customers.view'];

        foreach ($atendentePermissions as $slug) {
            $permission = $this->db->table('permissions')->where('slug', $slug)->get()->getRowArray();
            if ($permission) {
                $this->db->table('role_permissions')->insert([
                    'role_id' => 3,
                    'permission_id' => $permission['id'],
                ]);
            }
        }

        echo "Roles and Permissions seeded successfully.\n";
    }
}
