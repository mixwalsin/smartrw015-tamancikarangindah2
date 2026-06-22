<?php

/**
 * Admin\UserRoleController – Assign Role ke User
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

namespace Admin;

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/RbacService.php';
require_once APP_PATH . '/models/UserModel.php';
require_once APP_PATH . '/models/RoleModel.php';
require_once APP_PATH . '/models/AuditLogModel.php';

class UserRoleController extends \Controller
{
    private \UserModel    $userModel;
    private \RoleModel    $roleModel;
    private \AuditLogModel $auditLog;
    private \RbacService  $rbac;

    public function __construct()
    {
        $this->userModel = new \UserModel();
        $this->roleModel = new \RoleModel();
        $this->auditLog  = new \AuditLogModel();
        $this->rbac      = new \RbacService();
    }

    /**
     * Halaman assign role ke user.
     */
    public function index(): void
    {
        $this->requirePermission('user.assign_role');

        $this->view('admin/rbac/users/assign', [
            'title' => 'Assign Role ke Pengguna - ' . APP_NAME,
            'users' => $this->userModel->allWithRole('u.name'),
            'roles' => $this->roleModel->all('name'),
        ]);
    }

    /**
     * Proses assign role ke user.
     */
    public function assign(string $userId): void
    {
        $this->requirePermission('user.assign_role');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('admin/user-roles');
        }

        $user = $this->userModel->find((int) $userId);
        if (!$user) {
            setFlash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('admin/user-roles');
        }

        $roleId = (int) $this->input('role_id', 0);
        $role   = $this->roleModel->find($roleId);
        if (!$role) {
            setFlash('error', 'Role tidak ditemukan.');
            $this->redirect('admin/user-roles');
        }

        // Cegah escalation: non-super_admin tidak bisa assign super_admin role
        if ($role['slug'] === 'super_admin' && !$this->hasRole('super_admin')) {
            setFlash('error', 'Anda tidak diizinkan menetapkan role Super Admin.');
            $this->redirect('admin/user-roles');
        }

        $oldRole = $user['role_slug'] ?? $user['role'] ?? '-';
        $this->userModel->update((int) $userId, ['role_id' => $roleId]);
        $this->rbac->refreshPermissions((int) $userId);

        $this->logActivity(
            'assign_role',
            'users',
            (int) $userId,
            "Role user '{$user['name']}' diubah dari '{$oldRole}' ke '{$role['slug']}'"
        );

        setFlash('success', "Role pengguna '{$user['name']}' berhasil diubah ke '{$role['name']}'.");
        $this->redirect('admin/user-roles');
    }
}
