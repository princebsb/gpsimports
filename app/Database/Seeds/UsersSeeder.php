<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'password' => password_hash('Admin@123', PASSWORD_DEFAULT),
                'role_id' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Gerente',
                'email' => 'gerente@gpsimports.com.br',
                'password' => password_hash('Gerente@123', PASSWORD_DEFAULT),
                'role_id' => 2,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Atendente',
                'email' => 'atendente@gpsimports.com.br',
                'password' => password_hash('Atendente@123', PASSWORD_DEFAULT),
                'role_id' => 3,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->truncate();

        foreach ($users as $user) {
            $this->db->table('users')->insert($user);
        }

        echo "Users seeded successfully.\n";
    }
}
