<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'email',
        'token',
        'type',
        'used_at',
        'expires_at',
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Create reset token
     */
    public function createToken(string $email, string $type = 'customer'): string
    {
        // Invalidate old tokens
        $this->where('email', $email)
             ->where('type', $type)
             ->where('used_at IS NULL')
             ->delete();

        $token = bin2hex(random_bytes(32));

        $this->insert([
            'email' => $email,
            'token' => hash('sha256', $token),
            'type' => $type,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    /**
     * Validate token
     */
    public function validateToken(string $token, string $type = 'customer'): ?array
    {
        $hashedToken = hash('sha256', $token);

        $record = $this->where('token', $hashedToken)
                       ->where('type', $type)
                       ->where('used_at IS NULL')
                       ->where('expires_at >', date('Y-m-d H:i:s'))
                       ->first();

        return $record;
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(int $id): bool
    {
        return $this->update($id, ['used_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Clean expired tokens
     */
    public function cleanExpired(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }
}
