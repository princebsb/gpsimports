<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'role_id',
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'status',
        'last_login',
        'remember_token',
        'two_factor_secret',
        'two_factor_enabled',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[200]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[8]',
        'role_id' => 'required|integer',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Este email ja esta em uso.',
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
     * Verify user credentials
     */
    public function verifyCredentials(string $email, string $password): ?array
    {
        $user = $this->where('email', $email)
                     ->where('status', 'active')
                     ->first();

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        // Update last login
        $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        unset($user['password']);
        return $user;
    }

    /**
     * Get user with role
     */
    public function getUserWithRole(int $id): ?array
    {
        return $this->select('users.*, roles.name as role_name, roles.slug as role_slug')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.id', $id)
                    ->first();
    }

    /**
     * Get active users
     */
    public function getActive(): array
    {
        return $this->where('status', 'active')->findAll();
    }
}
