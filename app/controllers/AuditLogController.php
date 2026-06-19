<?php

declare(strict_types=1);

class AuditLogController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'log_aktivitas',
            'title' => 'Audit Log',
            'icon' => 'bi-journal-text',
            'route' => 'audit-log',
            'select_sql' => 'SELECT base.id, base.aksi, base.modul, base.keterangan, base.ip_address, base.created_at, users.name AS user_name FROM log_aktivitas base LEFT JOIN users ON users.id = base.user_id ORDER BY base.created_at DESC',
            'count_sql' => 'SELECT COUNT(*) FROM log_aktivitas base LEFT JOIN users ON users.id = base.user_id',
            'search_columns' => ['base.aksi', 'base.modul', 'base.keterangan', 'users.name'],
            'columns' => [
                ['key' => 'created_at', 'label' => 'Waktu'],
                ['key' => 'user_name', 'label' => 'Pengguna'],
                ['key' => 'aksi', 'label' => 'Aksi'],
                ['key' => 'modul', 'label' => 'Modul'],
                ['key' => 'keterangan', 'label' => 'Keterangan'],
            ],
            'fields' => [],
            'roles' => [
                'index' => ['admin', 'rw'],
            ],
        ];
    }

    public function create(): void
    {
        $this->redirect('audit-log');
    }

    public function store(): void
    {
        $this->redirect('audit-log');
    }

    public function edit(string $id): void
    {
        $this->redirect('audit-log/show/' . $id);
    }

    public function update(string $id): void
    {
        $this->redirect('audit-log/show/' . $id);
    }

    public function delete(string $id): void
    {
        $this->redirect('audit-log');
    }
}
