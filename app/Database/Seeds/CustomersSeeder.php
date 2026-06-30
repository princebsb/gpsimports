<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomersSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('customers')->truncate();
        $this->db->table('customer_addresses')->truncate();

        // Test customer
        $customers = [
            [
                'id' => 1,
                'name' => 'Cliente Teste',
                'email' => 'cliente@teste.com',
                'password' => password_hash('Cliente@123', PASSWORD_DEFAULT),
                'cpf' => '123.456.789-00',
                'phone' => '(11) 98765-4321',
                'birth_date' => '1990-05-15',
                'gender' => 'M',
                'status' => 'active',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'newsletter' => 1,
                'cashback_balance' => 50.00,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'name' => 'Maria Silva',
                'email' => 'maria@teste.com',
                'password' => password_hash('Maria@123', PASSWORD_DEFAULT),
                'cpf' => '987.654.321-00',
                'phone' => '(11) 91234-5678',
                'birth_date' => '1985-08-22',
                'gender' => 'F',
                'status' => 'active',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'newsletter' => 1,
                'cashback_balance' => 25.00,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'name' => 'Joao Santos',
                'email' => 'joao@teste.com',
                'password' => password_hash('Joao@123', PASSWORD_DEFAULT),
                'cpf' => '456.789.123-00',
                'phone' => '(21) 99876-5432',
                'birth_date' => '1992-03-10',
                'gender' => 'M',
                'status' => 'active',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'newsletter' => 0,
                'cashback_balance' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($customers as $customer) {
            $this->db->table('customers')->insert($customer);
        }

        // Addresses
        $addresses = [
            [
                'customer_id' => 1,
                'name' => 'Casa',
                'recipient' => 'Cliente Teste',
                'zipcode' => '01310100',
                'street' => 'Avenida Paulista',
                'number' => '1000',
                'complement' => 'Apto 101',
                'neighborhood' => 'Bela Vista',
                'city' => 'Sao Paulo',
                'state' => 'SP',
                'is_default' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'customer_id' => 1,
                'name' => 'Trabalho',
                'recipient' => 'Cliente Teste',
                'zipcode' => '04543011',
                'street' => 'Rua Funchal',
                'number' => '500',
                'complement' => 'Sala 201',
                'neighborhood' => 'Vila Olimpia',
                'city' => 'Sao Paulo',
                'state' => 'SP',
                'is_default' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'customer_id' => 2,
                'name' => 'Casa',
                'recipient' => 'Maria Silva',
                'zipcode' => '22041080',
                'street' => 'Rua Barata Ribeiro',
                'number' => '200',
                'complement' => '',
                'neighborhood' => 'Copacabana',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'is_default' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'customer_id' => 3,
                'name' => 'Casa',
                'recipient' => 'Joao Santos',
                'zipcode' => '30130000',
                'street' => 'Praca Sete de Setembro',
                'number' => '100',
                'complement' => 'Bloco A',
                'neighborhood' => 'Centro',
                'city' => 'Belo Horizonte',
                'state' => 'MG',
                'is_default' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($addresses as $address) {
            $this->db->table('customer_addresses')->insert($address);
        }

        echo "Customers seeded successfully.\n";
    }
}
