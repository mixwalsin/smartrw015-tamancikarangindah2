<?php

declare(strict_types=1);

class KeamananController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'security',
            'title' => 'Security',
            'icon' => 'bi-shield-check',
            'route' => 'keamanan',
            'select_sql' => 'SELECT base.id, base.tanggal, base.petugas, base.shift, base.status, rt.kode AS rt FROM security base LEFT JOIN rt ON rt.id = base.rt_id ORDER BY base.tanggal DESC',
            'count_sql' => 'SELECT COUNT(*) FROM security base LEFT JOIN rt ON rt.id = base.rt_id',
            'search_columns' => ['base.petugas', 'base.shift', 'base.status'],
            'columns' => [
                ['key' => 'tanggal', 'label' => 'Tanggal'],
                ['key' => 'petugas', 'label' => 'Petugas'],
                ['key' => 'shift', 'label' => 'Shift'],
                ['key' => 'status', 'label' => 'Status'],
            ],
            'fields' => [
                'rt_id' => ['label' => 'RT (opsional)', 'type' => 'select', 'options_callback' => 'getRtOptions'],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date', 'required' => true],
                'petugas' => ['label' => 'Petugas', 'type' => 'text', 'required' => true],
                'shift' => ['label' => 'Shift', 'type' => 'select', 'required' => true, 'options' => ['pagi' => 'Pagi', 'siang' => 'Siang', 'malam' => 'Malam']],
                'jam_mulai' => ['label' => 'Jam Mulai', 'type' => 'time'],
                'jam_selesai' => ['label' => 'Jam Selesai', 'type' => 'time'],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['terjadwal' => 'Terjadwal', 'aktif' => 'Aktif', 'selesai' => 'Selesai']],
                'dibuat_oleh' => ['label' => 'Dibuat Oleh', 'type' => 'number', 'default' => (string) (authUser()['id'] ?? 1)],
            ],
            'roles' => [
                'index' => ['admin', 'rw', 'rt'],
                'create' => ['admin', 'rw', 'rt'],
                'edit' => ['admin', 'rw', 'rt'],
                'delete' => ['admin', 'rw'],
            ],
        ];
    }

    protected function mutateBeforeSave(array $data, ?int $id): array
    {
        $data['dibuat_oleh'] = authUser()['id'] ?? 1;
        return $data;
    }
}
