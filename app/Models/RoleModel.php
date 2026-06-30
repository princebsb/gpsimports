<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name',
        'slug',
        'description',
        'is_system',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get role by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions(int $id): ?array
    {
        $role = $this->find($id);

        if (!$role) {
            return null;
        }

        $permissionModel = model('PermissionModel');
        $role['permissions'] = $permissionModel->getByRole($id);

        return $role;
    }

    /**
     * Assign permission to role
     */
    public function assignPermission(int $roleId, int $permissionId): bool
    {
        $db = \Config\Database::connect();

        return $db->table('role_permissions')->insert([
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Remove permission from role
     */
    public function removePermission(int $roleId, int $permissionId): bool
    {
        $db = \Config\Database::connect();

        return $db->table('role_permissions')
                  ->where('role_id', $roleId)
                  ->where('permission_id', $permissionId)
                  ->delete();
    }
}
