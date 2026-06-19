<?php

declare(strict_types=1);

class PengaduanController extends SimpleCrudController
{
    protected function boot(): void
    {
        $this->config = [
            'table' => 'pengaduan',
            'title' => 'Pengaduan',
            'icon' => 'bi-exclamation-octagon',
            'route' => 'pengaduan',
            'select_sql' => 'SELECT base.id, base.kategori, base.judul, base.status, base.created_at, warga.nama AS pelapor FROM pengaduan base LEFT JOIN warga ON warga.id = base.warga_id ORDER BY base.created_at DESC',
            'count_sql' => 'SELECT COUNT(*) FROM pengaduan base LEFT JOIN warga ON warga.id = base.warga_id',
            'search_columns' => ['base.kategori', 'base.judul', 'base.isi', 'warga.nama'],
            'columns' => [
                ['key' => 'kategori', 'label' => 'Kategori'],
                ['key' => 'judul', 'label' => 'Judul'],
                ['key' => 'pelapor', 'label' => 'Pelapor'],
                ['key' => 'status', 'label' => 'Status'],
            ],
            'fields' => [
                'warga_id' => ['label' => 'ID Warga', 'type' => 'number', 'default' => (string) (authUser()['warga_id'] ?? '' )],
                'kategori' => ['label' => 'Kategori', 'type' => 'text', 'required' => true],
                'judul' => ['label' => 'Judul', 'type' => 'text', 'required' => true],
                'isi' => ['label' => 'Isi Pengaduan', 'type' => 'textarea', 'required' => true],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'], 'default' => 'baru'],
                'catatan_admin' => ['label' => 'Catatan Admin', 'type' => 'textarea'],
            ],
            'roles' => [
                'index' => ['admin', 'rw', 'rt', 'warga'],
                'create' => ['admin', 'rw', 'rt', 'warga'],
                'edit' => ['admin', 'rw', 'rt'],
                'delete' => ['admin', 'rw'],
            ],
        ];
    }

    protected function mutateBeforeSave(array $data, ?int $id): array
    {
        if ((authUser()['role'] ?? '') === 'warga' && empty($data['warga_id'])) {
            $data['warga_id'] = authUser()['warga_id'] ?? null;
        }
        return $data;
    }

    public function updateStatus(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan');
        }

        $model = new PengaduanModel();
        $model->updateStatus((int) $id, (string) $this->input('status', 'baru'), trim((string) $this->input('catatan_admin', '')), (int) (authUser()['id'] ?? 0));
        logActivity('update_status', 'pengaduan', (int) $id, 'Memperbarui status pengaduan');
        setFlash('success', 'Status pengaduan diperbarui.');
        $this->redirect('pengaduan');
    }
}
