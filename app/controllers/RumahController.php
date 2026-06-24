<?php

declare(strict_types=1);

class RumahController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'rumah',
            'title' => 'Rumah',
            'icon' => 'bi-house-door',
            'route' => 'rumah',
            'select_sql' => 'SELECT base.id, base.no_urut, base.alamat, base.status_hunian, rt.kode AS rt, kk.no_kk FROM rumah base LEFT JOIN rt ON rt.id = base.rt_id LEFT JOIN kk ON kk.id = base.kk_id',
            'count_sql' => 'SELECT COUNT(*) FROM rumah base LEFT JOIN rt ON rt.id = base.rt_id LEFT JOIN kk ON kk.id = base.kk_id',
            'search_columns' => ['base.no_urut', 'base.alamat', 'base.status_hunian'],
            'columns' => [
                ['key' => 'no_urut', 'label' => 'No. Rumah'],
                ['key' => 'rt', 'label' => 'RT'],
                ['key' => 'status_hunian', 'label' => 'Status'],
                ['key' => 'alamat', 'label' => 'Alamat'],
            ],
            'fields' => [
                'rt_id' => ['label' => 'RT', 'type' => 'select', 'required' => true, 'options_callback' => 'getRtOptions'],
                'kk_id' => ['label' => 'ID KK', 'type' => 'number'],
                'no_urut' => ['label' => 'No. Rumah', 'type' => 'text', 'required' => true],
                'alamat' => ['label' => 'Alamat', 'type' => 'textarea', 'required' => true],
                'status_hunian' => ['label' => 'Status Hunian', 'type' => 'select', 'required' => true, 'options' => [
                    'milik' => 'Milik', 'sewa' => 'Sewa', 'kontrak' => 'Kontrak', 'kosong' => 'Kosong', 'lainnya' => 'Lainnya',
                ]],
                'keterangan' => ['label' => 'Keterangan', 'type' => 'textarea'],
            ],
            'roles' => [
                'index' => ['admin', 'rw', 'rt'],
                'create' => ['admin', 'rw', 'rt'],
                'edit' => ['admin', 'rw', 'rt'],
                'delete' => ['admin', 'rw'],
            ],
        ];
    }
}
