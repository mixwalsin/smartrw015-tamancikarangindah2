<?php

declare(strict_types=1);

class KegiatanController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'kegiatan',
            'title' => 'Kegiatan',
            'icon' => 'bi-calendar-event',
            'route' => 'kegiatan',
            'select_sql' => 'SELECT base.id, base.judul, base.tanggal, base.lokasi, base.is_published, rt.kode AS rt FROM kegiatan base LEFT JOIN rt ON rt.id = base.rt_id ORDER BY base.tanggal DESC',
            'count_sql' => 'SELECT COUNT(*) FROM kegiatan base LEFT JOIN rt ON rt.id = base.rt_id',
            'search_columns' => ['base.judul', 'base.lokasi', 'base.deskripsi'],
            'columns' => [
                ['key' => 'judul', 'label' => 'Judul'],
                ['key' => 'tanggal', 'label' => 'Tanggal'],
                ['key' => 'rt', 'label' => 'RT'],
                ['key' => 'lokasi', 'label' => 'Lokasi'],
            ],
            'fields' => [
                'rt_id' => ['label' => 'RT (opsional)', 'type' => 'select', 'options_callback' => 'getRtOptions'],
                'judul' => ['label' => 'Judul', 'type' => 'text', 'required' => true],
                'deskripsi' => ['label' => 'Deskripsi', 'type' => 'textarea'],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date', 'required' => true],
                'waktu_mulai' => ['label' => 'Mulai', 'type' => 'time'],
                'waktu_selesai' => ['label' => 'Selesai', 'type' => 'time'],
                'lokasi' => ['label' => 'Lokasi', 'type' => 'text'],
                'is_published' => ['label' => 'Publikasikan', 'type' => 'select', 'options' => ['0' => 'Draft', '1' => 'Publik'], 'default' => '1'],
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
        $data['rt_id'] = $data['rt_id'] ?: null;
        return $data;
    }
}
