<?php

declare(strict_types=1);

class PengumumanController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'pengumuman',
            'title' => 'Pengumuman',
            'icon' => 'bi-megaphone',
            'route' => 'pengumuman',
            'select_sql' => 'SELECT base.id, base.judul, base.jenis, base.is_published, base.published_at, rt.kode AS rt FROM pengumuman base LEFT JOIN rt ON rt.id = base.rt_id ORDER BY COALESCE(base.published_at, base.created_at) DESC',
            'count_sql' => 'SELECT COUNT(*) FROM pengumuman base LEFT JOIN rt ON rt.id = base.rt_id',
            'search_columns' => ['base.judul', 'base.isi', 'base.jenis'],
            'columns' => [
                ['key' => 'judul', 'label' => 'Judul'],
                ['key' => 'jenis', 'label' => 'Jenis'],
                ['key' => 'rt', 'label' => 'RT'],
                ['key' => 'published_at', 'label' => 'Publikasi'],
            ],
            'fields' => [
                'rt_id' => ['label' => 'RT (opsional)', 'type' => 'select', 'options_callback' => 'getRtOptions'],
                'judul' => ['label' => 'Judul', 'type' => 'text', 'required' => true],
                'isi' => ['label' => 'Isi Pengumuman', 'type' => 'textarea', 'required' => true],
                'jenis' => ['label' => 'Jenis', 'type' => 'select', 'required' => true, 'options' => ['umum' => 'Umum', 'penting' => 'Penting', 'darurat' => 'Darurat']],
                'is_published' => ['label' => 'Status', 'type' => 'select', 'options' => ['0' => 'Draft', '1' => 'Publikasikan'], 'default' => '1'],
                'published_at' => ['label' => 'Tanggal Publikasi', 'type' => 'datetime-local'],
                'dibuat_oleh' => ['label' => 'Dibuat Oleh', 'type' => 'number', 'default' => (string) (authUser()['id'] ?? 1)],
            ],
            'roles' => [
                'index' => ['admin', 'rw', 'rt', 'warga'],
                'create' => ['admin', 'rw', 'rt'],
                'edit' => ['admin', 'rw', 'rt'],
                'delete' => ['admin', 'rw'],
            ],
        ];
    }

    protected function mutateBeforeSave(array $data, ?int $id): array
    {
        $data['dibuat_oleh'] = authUser()['id'] ?? 1;
        if (!empty($data['published_at'])) {
            $data['published_at'] = str_replace('T', ' ', (string) $data['published_at']) . ':00';
        }
        return $data;
    }
}
