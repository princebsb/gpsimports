<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id',
        'user_type',
        'action',
        'model',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Log action
     */
    public function log(string $action, ?string $model = null, ?int $modelId = null, ?array $oldValues = null, ?array $newValues = null): bool
    {
        $session = session();

        return $this->insert([
            'user_id' => $session->get('admin_id') ?? $session->get('customer_id'),
            'user_type' => $session->get('admin_id') ? 'admin' : ($session->get('customer_id') ? 'customer' : 'system'),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => service('request')->getIPAddress(),
            'user_agent' => service('request')->getUserAgent()->getAgentString(),
            'url' => current_url(),
            'created_at' => date('Y-m-d H:i:s'),
        ]) !== false;
    }

    /**
     * Get recent logs
     */
    public function getRecent(int $limit = 100): array
    {
        return $this->select('audit_logs.*, users.name as user_name')
                    ->join('users', 'users.id = audit_logs.user_id AND audit_logs.user_type = "admin"', 'left')
                    ->orderBy('audit_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs by model
     */
    public function getByModel(string $model, int $modelId): array
    {
        return $this->where('model', $model)
                    ->where('model_id', $modelId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get logs by user
     */
    public function getByUser(int $userId, string $userType = 'admin'): array
    {
        return $this->where('user_id', $userId)
                    ->where('user_type', $userType)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Clean old logs
     */
    public function cleanOld(int $days = 90): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->where('created_at <', $date)->delete();
    }
}
