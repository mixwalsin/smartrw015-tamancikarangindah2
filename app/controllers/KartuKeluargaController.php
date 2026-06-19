<?php

declare(strict_types=1);

class KartuKeluargaController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'kk',
            'title' => 'Kartu Keluarga',
            'icon' => 'bi-card-list',
            'route' => 'kk',
            'select_sql' => 'SELECT kk.id, kk.no_kk, kk.alamat, kk.rt_text, rt.kode AS rt, rw.kode AS rw FROM kk kk LEFT JOIN rt rt ON rt.id = kk.rt_id LEFT JOIN rw rw ON rw.id = rt.rw_id',
            'count_sql' => 'SELECT COUNT(*) FROM kk kk LEFT JOIN rt rt ON rt.id = kk.rt_id',
            'search_columns' => ['kk.no_kk', 'kk.alamat', 'kk.rt_text'],
            'columns' => [
                ['key' => 'no_kk', 'label' => 'No. KK'],
                ['key' => 'rt', 'label' => 'RT'],
                ['key' => 'alamat', 'label' => 'Alamat'],
            ],
            'fields' => [
                'rt_id' => ['label' => 'RT', 'type' => 'select', 'required' => true, 'options_callback' => 'getRtOptions'],
                'no_kk' => ['label' => 'No. KK', 'type' => 'text', 'required' => true],
                'alamat' => ['label' => 'Alamat', 'type' => 'textarea', 'required' => true],
                'rt_text' => ['label' => 'Kode RT', 'type' => 'text'],
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
        if (!empty($data['rt_id'])) {
            $rt = $this->model->fetchOneQuery('SELECT kode FROM rt WHERE id = :id', ['id' => (int) $data['rt_id']]);
            $data['rt_text'] = $rt['kode'] ?? $data['rt_text'];
        }
        return $data;
    }
}
