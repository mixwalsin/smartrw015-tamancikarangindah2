<?php

declare(strict_types=1);

class SuratController extends Controller
{
    private GenericTableModel $suratModel;

    public function __construct()
    {
        $this->suratModel = new GenericTableModel('pengajuan_surat');
    }

    public function index(): void
    {
        $this->requireAuth();
        $page = max(1, (int) $this->query('page', 1));
        $pagination = $this->suratModel->paginateQuery(
            'SELECT base.id, base.no_surat, base.keperluan, base.status, base.created_at, surat.nama AS jenis_surat, warga.nama AS nama_warga FROM pengajuan_surat base INNER JOIN surat ON surat.id = base.surat_id INNER JOIN warga ON warga.id = base.warga_id ORDER BY base.created_at DESC',
            'SELECT COUNT(*) FROM pengajuan_surat base INNER JOIN surat ON surat.id = base.surat_id INNER JOIN warga ON warga.id = base.warga_id',
            $page,
            trim((string) $this->query('keyword', '')),
            ['surat.nama', 'warga.nama', 'base.keperluan', 'base.status']
        );

        $this->renderModuleIndex([
            'title' => 'Surat Online',
            'icon' => 'bi-envelope-paper',
            'routeBase' => 'surat',
            'columns' => [
                ['key' => 'jenis_surat', 'label' => 'Jenis Surat'],
                ['key' => 'nama_warga', 'label' => 'Pemohon'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'keperluan', 'label' => 'Keperluan'],
            ],
            'pagination' => $pagination,
            'canCreate' => true,
            'canEdit' => false,
            'canDelete' => false,
            'canShow' => true,
            'subtitle' => 'Pengajuan dan disposisi surat warga RW015.',
            'extraButtons' => [
                ['label' => 'Export Laporan', 'url' => url('laporan?module=surat'), 'class' => 'btn-outline-success'],
            ],
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $masterModel = new GenericTableModel('surat');
        $suratOptions = $masterModel->fetchPairs('SELECT id, nama FROM surat WHERE is_active = 1 ORDER BY nama ASC');

        $this->renderModuleForm([
            'title' => 'Ajukan Surat',
            'icon' => 'bi-envelope-plus',
            'routeBase' => 'surat',
            'actionUrl' => url('surat/store'),
            'submitText' => 'Ajukan',
            'fields' => [
                'surat_id' => ['label' => 'Jenis Surat', 'type' => 'select', 'required' => true, 'options' => $suratOptions],
                'warga_id' => ['label' => 'ID Warga', 'type' => 'number', 'required' => true, 'value' => authUser()['warga_id'] ?? ''],
                'keperluan' => ['label' => 'Keperluan', 'type' => 'textarea', 'required' => true],
                'status' => ['label' => 'Status Awal', 'type' => 'select', 'options' => ['pending' => 'Pending'], 'value' => 'pending'],
                'catatan' => ['label' => 'Catatan', 'type' => 'textarea'],
            ],
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('surat/create');
        }

        $wargaId = (int) $this->input('warga_id', authUser()['warga_id'] ?? 0);
        $id = $this->suratModel->insert([
            'surat_id' => (int) $this->input('surat_id', 0),
            'warga_id' => $wargaId,
            'keperluan' => trim((string) $this->input('keperluan', '')),
            'status' => 'pending',
            'catatan' => trim((string) $this->input('catatan', '')) ?: null,
        ]);

        logActivity('create', 'pengajuan_surat', (int) $id, 'Pengajuan surat baru');
        setFlash('success', 'Pengajuan surat berhasil dibuat.');
        $this->redirect('surat');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $row = $this->suratModel->fetchOneQuery(
            'SELECT base.*, surat.nama AS jenis_surat, warga.nama AS nama_warga, warga.nik
             FROM pengajuan_surat base
             INNER JOIN surat ON surat.id = base.surat_id
             INNER JOIN warga ON warga.id = base.warga_id
             WHERE base.id = :id',
            ['id' => (int) $id]
        );

        if (!$row) {
            $this->redirect('surat');
        }

        $this->renderModuleShow([
            'title' => 'Detail Pengajuan Surat',
            'icon' => 'bi-envelope-open',
            'routeBase' => 'surat',
            'row' => $row,
            'columns' => [
                ['key' => 'jenis_surat', 'label' => 'Jenis Surat'],
                ['key' => 'nama_warga', 'label' => 'Nama Pemohon'],
                ['key' => 'nik', 'label' => 'NIK'],
                ['key' => 'keperluan', 'label' => 'Keperluan'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'catatan', 'label' => 'Catatan'],
            ],
            'extraContent' => (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'], true) ? '<div class="d-flex gap-2 mt-3"><form method="POST" action="' . url('surat/approve/' . $id) . '">' . csrfField() . '<button class="btn btn-success btn-sm">Setujui</button></form><form method="POST" action="' . url('surat/reject/' . $id) . '">' . csrfField() . '<button class="btn btn-outline-danger btn-sm">Tolak</button></form><a class="btn btn-outline-primary btn-sm" href="' . url('surat/print/' . $id) . '">Print</a></div>' : ''),
        ]);
    }

    public function approve(string $id): void
    {
        $this->handleDecision((int) $id, 'selesai');
    }

    public function reject(string $id): void
    {
        $this->handleDecision((int) $id, 'ditolak');
    }

    public function print(string $id): void
    {
        $this->requireAuth();
        $row = $this->suratModel->fetchOneQuery(
            'SELECT base.*, surat.nama AS jenis_surat, warga.nama AS nama_warga, warga.nik, kk.alamat, rt.kode AS rt
             FROM pengajuan_surat base
             INNER JOIN surat ON surat.id = base.surat_id
             INNER JOIN warga ON warga.id = base.warga_id
             LEFT JOIN kk ON kk.id = warga.kk_id
             LEFT JOIN rt ON rt.id = kk.rt_id
             WHERE base.id = :id',
            ['id' => (int) $id]
        );
        if (!$row) {
            $this->redirect('surat');
        }

        $this->view('shared/print_surat', [
            'title' => 'Cetak Surat',
            'row' => $row,
        ], null);
    }

    private function handleDecision(int $id, string $status): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('surat');
        }

        $data = ['status' => $status, 'disetujui_oleh' => authUser()['id'] ?? 1];
        if ($status === 'selesai') {
            $data['disetujui_at'] = date('Y-m-d H:i:s');
            $data['no_surat'] = 'RW015/' . date('Ym') . '/' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
        }
        $this->suratModel->update($id, $data);
        logActivity($status === 'selesai' ? 'approve' : 'reject', 'pengajuan_surat', $id, 'Memproses surat warga');
        setFlash('success', 'Status pengajuan surat diperbarui.');
        $this->redirect('surat/show/' . $id);
    }
}
