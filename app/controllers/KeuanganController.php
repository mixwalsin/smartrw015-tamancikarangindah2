<?php

declare(strict_types=1);

class KeuanganController extends Controller
{
    private GenericTableModel $kasRwModel;
    private GenericTableModel $kasRtModel;

    public function __construct()
    {
        $this->kasRwModel = new GenericTableModel('kas_rw');
        $this->kasRtModel = new GenericTableModel('kas_rt');
    }

    public function index(): void
    {
        $this->requireAuth();
        $tab = (string) $this->query('tab', 'rw');
        $page = max(1, (int) $this->query('page', 1));

        if ($tab === 'rt') {
            $pagination = $this->kasRtModel->paginateQuery(
                'SELECT base.id, base.tanggal, base.jenis, base.kategori, base.jumlah, base.saldo_setelah, rt.kode AS rt FROM kas_rt base LEFT JOIN rt ON rt.id = base.rt_id ORDER BY base.tanggal DESC',
                'SELECT COUNT(*) FROM kas_rt base LEFT JOIN rt ON rt.id = base.rt_id',
                $page,
                trim((string) $this->query('keyword', '')),
                ['base.kategori', 'base.keterangan', 'rt.kode']
            );
        } else {
            $pagination = $this->kasRwModel->paginateQuery(
                'SELECT base.id, base.tanggal, base.jenis, base.kategori, base.jumlah, base.saldo_setelah FROM kas_rw base ORDER BY base.tanggal DESC',
                'SELECT COUNT(*) FROM kas_rw base',
                $page,
                trim((string) $this->query('keyword', '')),
                ['base.kategori', 'base.keterangan']
            );
        }

        $this->renderModuleIndex([
            'title' => 'Kas ' . strtoupper($tab),
            'icon' => 'bi-cash-stack',
            'routeBase' => 'keuangan',
            'columns' => [
                ['key' => 'tanggal', 'label' => 'Tanggal'],
                ['key' => 'jenis', 'label' => 'Jenis'],
                ['key' => 'kategori', 'label' => 'Kategori'],
                ['key' => 'jumlah', 'label' => 'Jumlah'],
                ['key' => 'saldo_setelah', 'label' => 'Saldo'],
            ],
            'pagination' => $pagination,
            'canCreate' => in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'], true),
            'canEdit' => false,
            'canDelete' => false,
            'canShow' => false,
            'subtitle' => 'Gunakan tab query ?tab=rw atau ?tab=rt untuk melihat kas RW/RT.',
            'extraButtons' => [
                ['label' => 'Kas RW', 'url' => url('keuangan?tab=rw'), 'class' => $tab === 'rw' ? 'btn-primary' : 'btn-outline-primary'],
                ['label' => 'Kas RT', 'url' => url('keuangan?tab=rt'), 'class' => $tab === 'rt' ? 'btn-primary' : 'btn-outline-primary'],
            ],
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $tab = (string) $this->query('tab', 'rw');

        $fields = [
            'tanggal' => ['label' => 'Tanggal', 'type' => 'date', 'required' => true],
            'jenis' => ['label' => 'Jenis', 'type' => 'select', 'required' => true, 'options' => ['pemasukan' => 'Pemasukan', 'pengeluaran' => 'Pengeluaran']],
            'kategori' => ['label' => 'Kategori', 'type' => 'text', 'required' => true],
            'keterangan' => ['label' => 'Keterangan', 'type' => 'textarea'],
            'jumlah' => ['label' => 'Jumlah', 'type' => 'number', 'required' => true],
        ];

        if ($tab === 'rt') {
            $fields = ['rt_id' => ['label' => 'RT', 'type' => 'select', 'required' => true, 'options' => $this->kasRtModel->fetchPairs('SELECT id, CONCAT("RT ", kode) FROM rt ORDER BY kode')]] + $fields;
        }

        $this->renderModuleForm([
            'title' => 'Tambah Kas ' . strtoupper($tab),
            'icon' => 'bi-cash-stack',
            'routeBase' => 'keuangan',
            'actionUrl' => url('keuangan/store?tab=' . $tab),
            'fields' => $fields,
            'submitText' => 'Simpan',
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('keuangan');
        }

        $tab = (string) $this->query('tab', 'rw');
        $tanggal = (string) $this->input('tanggal', '');
        $jenis = (string) $this->input('jenis', '');
        $kategori = trim((string) $this->input('kategori', ''));
        $keterangan = trim((string) $this->input('keterangan', ''));
        $jumlah = (float) $this->input('jumlah', 0);

        if ($tanggal === '' || $jenis === '' || $kategori === '' || $jumlah <= 0) {
            setFlash('error', 'Tanggal, jenis, kategori, dan jumlah wajib diisi.');
            $this->redirect('keuangan/create?tab=' . $tab);
        }

        if ($tab === 'rt') {
            $rtId = (int) $this->input('rt_id', 0);
            $last = $this->kasRtModel->fetchOneQuery('SELECT saldo_setelah FROM kas_rt WHERE rt_id = :rt_id ORDER BY tanggal DESC, id DESC LIMIT 1', ['rt_id' => $rtId]);
            $saldo = (float) ($last['saldo_setelah'] ?? 0);
            $saldo = $jenis === 'pemasukan' ? $saldo + $jumlah : $saldo - $jumlah;
            $this->kasRtModel->insert([
                'rt_id' => $rtId,
                'tanggal' => $tanggal,
                'jenis' => $jenis,
                'kategori' => $kategori,
                'keterangan' => $keterangan ?: null,
                'jumlah' => $jumlah,
                'saldo_setelah' => $saldo,
                'dibuat_oleh' => authUser()['id'] ?? 1,
            ]);
            logActivity('create', 'kas_rt', null, 'Menambah transaksi kas RT');
        } else {
            $last = $this->kasRwModel->fetchOneQuery('SELECT saldo_setelah FROM kas_rw ORDER BY tanggal DESC, id DESC LIMIT 1');
            $saldo = (float) ($last['saldo_setelah'] ?? 0);
            $saldo = $jenis === 'pemasukan' ? $saldo + $jumlah : $saldo - $jumlah;
            $this->kasRwModel->insert([
                'tanggal' => $tanggal,
                'jenis' => $jenis,
                'kategori' => $kategori,
                'keterangan' => $keterangan ?: null,
                'jumlah' => $jumlah,
                'saldo_setelah' => $saldo,
                'dibuat_oleh' => authUser()['id'] ?? 1,
            ]);
            logActivity('create', 'kas_rw', null, 'Menambah transaksi kas RW');
        }

        setFlash('success', 'Transaksi kas berhasil disimpan.');
        $this->redirect('keuangan?tab=' . $tab);
    }

    public function show(string $id): void
    {
        $this->redirect('keuangan');
    }
}
