<?php

/**
 * Admin\RoleController – Manajemen Role RBAC
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

namespace Admin;

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/RbacService.php';
require_once APP_PATH . '/models/RoleModel.php';
require_once APP_PATH . '/models/PermissionModel.php';
require_once APP_PATH . '/models/AuditLogModel.php';

class RoleController extends \Controller
{
    private \RoleModel       $roleModel;
    private \PermissionModel $permModel;
    private \AuditLogModel   $auditLog;
    private \RbacService     $rbac;

    public function __construct()
    {
        $this->roleModel = new \RoleModel();
        $this->permModel = new \PermissionModel();
        $this->auditLog  = new \AuditLogModel();
        $this->rbac      = new \RbacService();
    }

    // ──────────────────────────────────────────
    // Index
    // ──────────────────────────────────────────

    public function index(): void
    {
        $this->requirePermission('role.read');

        $this->view('admin/rbac/roles/index', [
            'title' => 'Manajemen Role - ' . APP_NAME,
            'roles' => $this->roleModel->allWithStats(),
        ]);
    }

    // ──────────────────────────────────────────
    // Create
    // ──────────────────────────────────────────

    public function create(): void
    {
        $this->requirePermission('role.create');
        $this->view('admin/rbac/roles/create', [
            'title' => 'Tambah Role - ' . APP_NAME,
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('role.create');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('admin/roles/create');
        }

        $name        = trim($this->input('name', ''));
        $slug        = trim($this->input('slug', ''));
        $description = trim($this->input('description', ''));

        if ($name === '' || $slug === '') {
            setFlash('error', 'Nama dan slug wajib diisi.');
            $this->redirect('admin/roles/create');
        }

        if ($this->roleModel->isSlugTaken($slug)) {
            setFlash('error', 'Slug sudah digunakan.');
            $this->redirect('admin/roles/create');
        }

        $id = $this->roleModel->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
        ]);

        $this->logActivity('create', 'rbac_roles', (int) $id, "Role dibuat: {$name}");
        setFlash('success', "Role '{$name}' berhasil ditambahkan.");
        $this->redirect('admin/roles');
    }

    // ──────────────────────────────────────────
    // Edit
    // ──────────────────────────────────────────

    public function edit(string $id): void
    {
        $this->requirePermission('role.update');

        $role = $this->roleModel->find((int) $id);
        if (!$role) {
            setFlash('error', 'Role tidak ditemukan.');
            $this->redirect('admin/roles');
        }

        $this->view('admin/rbac/roles/edit', [
            'title' => 'Edit Role - ' . APP_NAME,
            'role'  => $role,
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('role.update');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('admin/roles/edit/' . $id);
        }

        $role = $this->roleModel->find((int) $id);
        if (!$role) {
            setFlash('error', 'Role tidak ditemukan.');
            $this->redirect('admin/roles');
        }

        $name        = trim($this->input('name', ''));
        $slug        = trim($this->input('slug', ''));
        $description = trim($this->input('description', ''));

        if ($name === '' || $slug === '') {
            setFlash('error', 'Nama dan slug wajib diisi.');
            $this->redirect('admin/roles/edit/' . $id);
        }

        if ($this->roleModel->isSlugTaken($slug, (int) $id)) {
            setFlash('error', 'Slug sudah digunakan.');
            $this->redirect('admin/roles/edit/' . $id);
        }

        $this->roleModel->update((int) $id, [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
        ]);

        // Invalidate permission cache for all users with this role
        $users = $this->roleModel->getUsers((int) $id);
        foreach ($users as $u) {
            $this->rbac->refreshPermissions((int) $u['id']);
        }

        $this->logActivity('update', 'rbac_roles', (int) $id, "Role diubah: {$name}");
        setFlash('success', "Role '{$name}' berhasil diperbarui.");
        $this->redirect('admin/roles');
    }

    // ──────────────────────────────────────────
    // Delete
    // ──────────────────────────────────────────

    public function delete(string $id): void
    {
        $this->requirePermission('role.delete');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('admin/roles');
        }

        $role = $this->roleModel->find((int) $id);
        if (!$role) {
            setFlash('error', 'Role tidak ditemukan.');
            $this->redirect('admin/roles');
        }

        // Cegah hapus role Super Admin (id=1)
        if ((int) $id === 1) {
            setFlash('error', 'Role Super Admin tidak dapat dihapus.');
            $this->redirect('admin/roles');
        }

        $totalUsers = count($this->roleModel->getUsers((int) $id));
        if ($totalUsers > 0) {
            setFlash('error', "Role tidak dapat dihapus karena masih digunakan oleh {$totalUsers} pengguna.");
            $this->redirect('admin/roles');
        }

        $this->roleModel->delete((int) $id);
        $this->logActivity('delete', 'rbac_roles', (int) $id, "Role dihapus: {$role['name']}");
        setFlash('success', "Role '{$role['name']}' berhasil dihapus.");
        $this->redirect('admin/roles');
    }

    // ──────────────────────────────────────────
    // Assign Permissions
    // ──────────────────────────────────────────

    public function permissions(string $id): void
    {
        $this->requirePermission('role.assign_permission');

        $role = $this->roleModel->find((int) $id);
        if (!$role) {
            setFlash('error', 'Role tidak ditemukan.');
            $this->redirect('admin/roles');
        }

        $this->view('admin/rbac/roles/permissions', [
            'title'           => 'Kelola Permission Role - ' . APP_NAME,
            'role'            => $role,
            'allPermissions'  => $this->permModel->allGrouped(),
            'rolePermissions' => $this->roleModel->getPermissionIds((int) $id),
        ]);
    }

    public function syncPermissions(string $id): void
    {
        $this->requirePermission('role.assign_permission');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('admin/roles/permissions/' . $id);
        }

        $role = $this->roleModel->find((int) $id);
        if (!$role) {
            setFlash('error', 'Role tidak ditemukan.');
            $this->redirect('admin/roles');
        }

        $permissionIds = array_map('intval', (array) ($this->input('permissions') ?? []));
        $this->roleModel->syncPermissions((int) $id, $permissionIds);

        // Invalidate permission cache for all users with this role
        $users = $this->roleModel->getUsers((int) $id);
        foreach ($users as $u) {
            $this->rbac->refreshPermissions((int) $u['id']);
        }

        $this->logActivity(
            'assign_permission',
            'rbac_roles',
            (int) $id,
            "Permission role '{$role['name']}' diperbarui: " . count($permissionIds) . " permissions"
        );
        setFlash('success', "Permission role '{$role['name']}' berhasil diperbarui.");
        $this->redirect('admin/roles/permissions/' . $id);
    }
}
