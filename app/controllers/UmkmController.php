<?php

declare(strict_types=1);

class UmkmController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'umkm',
            'title' => 'UMKM',
            'icon' => 'bi-shop',
            'route' => 'umkm',
            'select_sql' => 'SELECT base.id, base.nama_usaha, base.kategori, base.no_hp, base.status, rt.kode AS rt, warga.nama AS pemilik FROM umkm base LEFT JOIN rt ON rt.id = base.rt_id LEFT JOIN warga ON warga.id = base.warga_id ORDER BY base.created_at DESC',
            'count_sql' => 'SELECT COUNT(*) FROM umkm base LEFT JOIN rt ON rt.id = base.rt_id LEFT JOIN warga ON warga.id = base.warga_id',
            'search_columns' => ['base.nama_usaha', 'base.kategori', 'base.no_hp', 'warga.nama'],
            'columns' => [
                ['key' => 'nama_usaha', 'label' => 'Nama Usaha'],
                ['key' => 'pemilik', 'label' => 'Pemilik'],
                ['key' => 'kategori', 'label' => 'Kategori'],
                ['key' => 'status', 'label' => 'Status'],
            ],
            'fields' => [
                'warga_id' => ['label' => 'ID Warga', 'type' => 'number'],
                'rt_id' => ['label' => 'RT', 'type' => 'select', 'options_callback' => 'getRtOptions'],
                'nama_usaha' => ['label' => 'Nama Usaha', 'type' => 'text', 'required' => true],
                'kategori' => ['label' => 'Kategori', 'type' => 'text'],
                'deskripsi' => ['label' => 'Deskripsi', 'type' => 'textarea'],
                'produk' => ['label' => 'Produk / Layanan', 'type' => 'textarea'],
                'alamat' => ['label' => 'Alamat', 'type' => 'textarea'],
                'no_hp' => ['label' => 'No. HP', 'type' => 'text'],
                'email' => ['label' => 'Email', 'type' => 'email'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['aktif' => 'Aktif', 'tidak_aktif' => 'Tidak Aktif'], 'default' => 'aktif'],
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
        return $data;
    }
}
