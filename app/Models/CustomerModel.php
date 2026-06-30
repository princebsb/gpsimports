<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'name',
        'email',
        'password',
        'cpf',
        'cnpj',
        'phone',
        'mobile',
        'birth_date',
        'gender',
        'avatar',
        'status',
        'email_verified_at',
        'newsletter',
        'cashback_balance',
        'total_orders',
        'total_spent',
        'last_order_at',
        'last_login_at',
        'remember_token',
        'notes',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[200]',
        'email' => 'required|valid_email|is_unique[customers.email,id,{id}]',
        'password' => 'permit_empty|min_length[8]',
        'cpf' => 'permit_empty|min_length[11]|max_length[14]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Este email ja esta cadastrado.',
        ],
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before save
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['password']);
        }

        return $data;
    }

    /**
     * Verify customer credentials
     */
    public function verifyCredentials(string $email, string $password): ?array
    {
        $customer = $this->where('email', $email)
                         ->where('status', 'active')
                         ->first();

        if (!$customer) {
            return null;
        }

        if (!password_verify($password, $customer['password'])) {
            return null;
        }

        // Update last login
        $this->update($customer['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        unset($customer['password']);
        return $customer;
    }

    /**
     * Get customer by email
     */
    public function getByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Update customer stats after order
     */
    public function updateOrderStats(int $customerId, float $orderTotal): bool
    {
        $customer = $this->find($customerId);

        if (!$customer) {
            return false;
        }

        return $this->update($customerId, [
            'total_orders' => $customer['total_orders'] + 1,
            'total_spent' => $customer['total_spent'] + $orderTotal,
            'last_order_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Add cashback to customer
     */
    public function addCashback(int $customerId, float $amount): bool
    {
        $customer = $this->find($customerId);

        if (!$customer) {
            return false;
        }

        return $this->update($customerId, [
            'cashback_balance' => $customer['cashback_balance'] + $amount,
        ]);
    }

    /**
     * Use cashback
     */
    public function useCashback(int $customerId, float $amount): bool
    {
        $customer = $this->find($customerId);

        if (!$customer || $customer['cashback_balance'] < $amount) {
            return false;
        }

        return $this->update($customerId, [
            'cashback_balance' => $customer['cashback_balance'] - $amount,
        ]);
    }

    /**
     * Search customers
     */
    public function search(string $term, int $limit = 20): array
    {
        return $this->groupStart()
                    ->like('name', $term)
                    ->orLike('email', $term)
                    ->orLike('cpf', $term)
                    ->orLike('phone', $term)
                    ->groupEnd()
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get customers for export
     */
    public function getForExport(array $filters = []): array
    {
        $builder = $this->builder();

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $builder->where('created_at >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $builder->where('created_at <=', $filters['to_date'] . ' 23:59:59');
        }

        return $builder->get()->getResultArray();
    }
}
