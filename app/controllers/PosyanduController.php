<?php

declare(strict_types=1);

class PosyanduController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'posyandu',
            'title' => 'Posyandu',
            'icon' => 'bi-heart-pulse',
            'route' => 'posyandu',
            'select_sql' => 'SELECT base.id, base.tanggal, base.jenis_kegiatan, base.status_gizi, warga.nama AS nama_warga FROM posyandu base LEFT JOIN warga ON warga.id = base.warga_id ORDER BY base.tanggal DESC',
            'count_sql' => 'SELECT COUNT(*) FROM posyandu base LEFT JOIN warga ON warga.id = base.warga_id',
            'search_columns' => ['warga.nama', 'base.jenis_kegiatan', 'base.status_gizi'],
            'columns' => [
                ['key' => 'tanggal', 'label' => 'Tanggal'],
                ['key' => 'nama_warga', 'label' => 'Warga'],
                ['key' => 'jenis_kegiatan', 'label' => 'Jenis'],
                ['key' => 'status_gizi', 'label' => 'Status Gizi'],
            ],
            'fields' => [
                'warga_id' => ['label' => 'ID Warga', 'type' => 'number', 'required' => true],
                'tanggal' => ['label' => 'Tanggal', 'type' => 'date', 'required' => true],
                'jenis_kegiatan' => ['label' => 'Jenis Kegiatan', 'type' => 'select', 'required' => true, 'options' => [
                    'balita' => 'Balita', 'lansia' => 'Lansia', 'ibu_hamil' => 'Ibu Hamil', 'remaja' => 'Remaja', 'umum' => 'Umum',
                ]],
                'berat_badan' => ['label' => 'Berat Badan', 'type' => 'number'],
                'tinggi_badan' => ['label' => 'Tinggi Badan', 'type' => 'number'],
                'status_gizi' => ['label' => 'Status Gizi', 'type' => 'text'],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
                'petugas_id' => ['label' => 'Petugas ID', 'type' => 'number', 'default' => (string) (authUser()['id'] ?? 1)],
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
        $data['petugas_id'] = authUser()['id'] ?? 1;
        return $data;
    }
}
