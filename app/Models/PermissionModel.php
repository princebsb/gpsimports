<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name',
        'slug',
        'module',
        'description',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get permissions by role
     */
    public function getByRole(int $roleId): array
    {
        return $this->select('permissions.*')
                    ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
                    ->where('role_permissions.role_id', $roleId)
                    ->findAll();
    }

    /**
     * Get permissions grouped by module
     */
    public function getGroupedByModule(): array
    {
        $permissions = $this->orderBy('module')->findAll();
        $grouped = [];

        foreach ($permissions as $permission) {
            $grouped[$permission['module']][] = $permission;
        }

        return $grouped;
    }

    /**
     * Check if role has permission
     */
    public function roleHasPermission(int $roleId, string $permissionSlug): bool
    {
        $db = \Config\Database::connect();

        return $db->table('role_permissions')
                  ->join('permissions', 'permissions.id = role_permissions.permission_id')
                  ->where('role_permissions.role_id', $roleId)
                  ->where('permissions.slug', $permissionSlug)
                  ->countAllResults() > 0;
    }
}
