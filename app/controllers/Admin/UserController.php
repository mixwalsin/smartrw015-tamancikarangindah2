<?php

declare(strict_types=1);

namespace Admin;

class UserController extends \SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'users',
            'title' => 'Pengguna',
            'icon' => 'bi-person-gear',
            'route' => 'admin/users',
            'select_sql' => 'SELECT base.id, base.name, base.username, base.email, base.is_active, roles.name AS role_name FROM users base INNER JOIN roles ON roles.id = base.role_id ORDER BY base.created_at DESC',
            'count_sql' => 'SELECT COUNT(*) FROM users base INNER JOIN roles ON roles.id = base.role_id',
            'search_columns' => ['base.name', 'base.username', 'base.email', 'roles.name'],
            'columns' => [
                ['key' => 'name', 'label' => 'Nama'],
                ['key' => 'username', 'label' => 'Username'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'role_name', 'label' => 'Role'],
            ],
            'fields' => [
                'role_id' => ['label' => 'Role', 'type' => 'select', 'required' => true, 'options_callback' => 'getRoleOptions'],
                'warga_id' => ['label' => 'ID Warga', 'type' => 'number'],
                'name' => ['label' => 'Nama', 'type' => 'text', 'required' => true],
                'username' => ['label' => 'Username', 'type' => 'text', 'required' => true],
                'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
                'password' => ['label' => 'Password', 'type' => 'password'],
                'is_active' => ['label' => 'Aktif', 'type' => 'select', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif'], 'default' => '1'],
            ],
            'roles' => [
                'index' => ['admin', 'rw'],
                'create' => ['admin'],
                'edit' => ['admin'],
                'delete' => ['admin'],
            ],
        ];
    }

    protected function mutateBeforeSave(array $data, ?int $id): array
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash((string) $data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        return $data;
    }
}
