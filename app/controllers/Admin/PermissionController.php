<?php

/**
 * Admin\PermissionController – Manajemen Permission RBAC
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

namespace Admin;

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/RbacService.php';
require_once APP_PATH . '/models/PermissionModel.php';
require_once APP_PATH . '/models/AuditLogModel.php';

class PermissionController extends \Controller
{
    private \PermissionModel $permModel;
    private \AuditLogModel   $auditLog;

    public function __construct()
    {
        $this->permModel = new \PermissionModel();
        $this->auditLog  = new \AuditLogModel();
    }

    // ──────────────────────────────────────────
    // Index
    // ──────────────────────────────────────────

    public function index(): void
    {
        $this->requirePermission('permission.read');

        $this->view('admin/rbac/permissions/index', [
            'title'       => 'Manajemen Permission - ' . APP_NAME,
            'permissions' => $this->permModel->allWithRoles(),
            'moduls'      => $this->permModel->getModuls(),
        ]);
    }

    // ──────────────────────────────────────────
    // Create
    // ──────────────────────────────────────────

    public function create(): void
    {
        $this->requirePermission('permission.create');
        $this->view('admin/rbac/permissions/create', [
            'title'  => 'Tambah Permission - ' . APP_NAME,
            'moduls' => $this->permModel->getModuls(),
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('permission.create');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('admin/permissions/create');
        }

        $name        = trim($this->input('name', ''));
        $slug        = trim($this->input('slug', ''));
        $modul       = trim($this->input('modul', ''));
        $description = trim($this->input('description', ''));

        if ($name === '' || $slug === '' || $modul === '') {
            setFlash('error', 'Nama, slug, dan modul wajib diisi.');
            $this->redirect('admin/permissions/create');
        }

        if ($this->permModel->isSlugTaken($slug)) {
            setFlash('error', 'Slug permission sudah digunakan.');
            $this->redirect('admin/permissions/create');
        }

        $id = $this->permModel->insert([
            'name'        => $name,
            'slug'        => $slug,
            'modul'       => $modul,
            'description' => $description,
        ]);

        $this->logActivity('create', 'rbac_permissions', (int) $id, "Permission dibuat: {$slug}");
        setFlash('success', "Permission '{$slug}' berhasil ditambahkan.");
        $this->redirect('admin/permissions');
    }

    // ──────────────────────────────────────────
    // Edit
    // ──────────────────────────────────────────

    public function edit(string $id): void
    {
        $this->requirePermission('permission.update');

        $perm = $this->permModel->find((int) $id);
        if (!$perm) {
            setFlash('error', 'Permission tidak ditemukan.');
            $this->redirect('admin/permissions');
        }

        $this->view('admin/rbac/permissions/edit', [
            'title'  => 'Edit Permission - ' . APP_NAME,
            'perm'   => $perm,
            'moduls' => $this->permModel->getModuls(),
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('permission.update');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('admin/permissions/edit/' . $id);
        }

        $perm = $this->permModel->find((int) $id);
        if (!$perm) {
            setFlash('error', 'Permission tidak ditemukan.');
            $this->redirect('admin/permissions');
        }

        $name        = trim($this->input('name', ''));
        $slug        = trim($this->input('slug', ''));
        $modul       = trim($this->input('modul', ''));
        $description = trim($this->input('description', ''));

        if ($name === '' || $slug === '' || $modul === '') {
            setFlash('error', 'Nama, slug, dan modul wajib diisi.');
            $this->redirect('admin/permissions/edit/' . $id);
        }

        if ($this->permModel->isSlugTaken($slug, (int) $id)) {
            setFlash('error', 'Slug permission sudah digunakan.');
            $this->redirect('admin/permissions/edit/' . $id);
        }

        $this->permModel->update((int) $id, [
            'name'        => $name,
            'slug'        => $slug,
            'modul'       => $modul,
            'description' => $description,
        ]);

        $this->logActivity('update', 'rbac_permissions', (int) $id, "Permission diubah: {$slug}");
        setFlash('success', "Permission '{$slug}' berhasil diperbarui.");
        $this->redirect('admin/permissions');
    }

    // ──────────────────────────────────────────
    // Delete
    // ──────────────────────────────────────────

    public function delete(string $id): void
    {
        $this->requirePermission('permission.delete');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('admin/permissions');
        }

        $perm = $this->permModel->find((int) $id);
        if (!$perm) {
            setFlash('error', 'Permission tidak ditemukan.');
            $this->redirect('admin/permissions');
        }

        $this->permModel->delete((int) $id);
        $this->logActivity('delete', 'rbac_permissions', (int) $id, "Permission dihapus: {$perm['slug']}");
        setFlash('success', "Permission '{$perm['slug']}' berhasil dihapus.");
        $this->redirect('admin/permissions');
    }
}
