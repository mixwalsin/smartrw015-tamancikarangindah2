<?php

/**
 * KartuKeluargaController
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/KartuKeluargaModel.php';
require_once APP_PATH . '/models/WargaModel.php';

class KartuKeluargaController extends Controller
{
    private KartuKeluargaModel $model;
    private WargaModel $wargaModel;

    /** RT list: [kode => rt_id] */
    private const RT_LIST = [
        '001' => 1, '002' => 2, '003' => 3, '004' => 4,
        '005' => 5, '006' => 6, '007' => 7,
    ];

    /** Hubungan options */
    private const HUBUNGAN_LIST = [
        'Kepala Keluarga', 'Istri', 'Anak', 'Menantu', 'Cucu',
        'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya',
    ];

    public function __construct()
    {
        $this->model      = new KartuKeluargaModel();
        $this->wargaModel = new WargaModel();
    }

    // ──────────────────────────────────────────
    // Daftar KK
    // ──────────────────────────────────────────

    public function index(): void
    {
        $this->requireAuth();
        $page    = (int) ($this->query('page', 1));
        $keyword = trim($this->query('keyword', ''));

        $pagination = $this->model->listWithDetail($page, $keyword);

        $this->view('kartukeluarga/index', [
            'title'      => 'Kartu Keluarga - ' . APP_NAME,
            'pagination' => $pagination,
        ]);
    }

    // ──────────────────────────────────────────
    // Tambah KK
    // ──────────────────────────────────────────

    public function create(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        $this->view('kartukeluarga/create', [
            'title'  => 'Tambah Kartu Keluarga - ' . APP_NAME,
            'rtList' => self::RT_LIST,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('kartukeluarga/create');
        }

        $noKk   = trim($this->input('no_kk', ''));
        $alamat = trim($this->input('alamat', ''));
        $rtText = $this->input('rt', '');
        $rtId   = (int) $this->input('rt_id', 0);

        if ($noKk === '' || $alamat === '' || $rtText === '' || $rtId === 0) {
            setFlash('error', 'No. KK, Alamat, dan RT wajib diisi.');
            $this->redirect('kartukeluarga/create');
        }

        if (!validateNik($noKk)) {
            setFlash('error', 'No. KK harus terdiri dari 16 digit angka.');
            $this->redirect('kartukeluarga/create');
        }

        if ($this->model->findByNoKk($noKk)) {
            setFlash('error', 'Nomor KK sudah terdaftar.');
            $this->redirect('kartukeluarga/create');
        }

        $kkId = $this->model->insert([
            'rt_id'   => $rtId,
            'no_kk'   => $noKk,
            'alamat'  => $alamat,
            'rt_text' => $rtText,
        ]);

        $userId = $_SESSION['user']['id'] ?? null;
        $this->model->logRiwayat(
            (int) $kkId,
            'tambah_kk',
            null,
            "KK {$noKk} ditambahkan.",
            $userId
        );

        setFlash('success', 'Kartu Keluarga berhasil ditambahkan.');
        $this->redirect('kartukeluarga/show/' . $kkId);
    }

    // ──────────────────────────────────────────
    // Detail KK
    // ──────────────────────────────────────────

    public function show(string $id): void
    {
        $this->requireAuth();
        $kk = $this->model->findWithKepala((int) $id);
        if (!$kk) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        $anggota = $this->model->getAnggota((int) $id);

        $this->view('kartukeluarga/show', [
            'title'   => 'Detail KK ' . $kk['no_kk'] . ' - ' . APP_NAME,
            'kk'      => $kk,
            'anggota' => $anggota,
        ]);
    }

    // ──────────────────────────────────────────
    // Edit KK
    // ──────────────────────────────────────────

    public function edit(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        $kk = $this->model->find((int) $id);
        if (!$kk) {
            $this->redirect('kartukeluarga');
        }

        $this->view('kartukeluarga/edit', [
            'title'  => 'Edit Kartu Keluarga - ' . APP_NAME,
            'kk'     => $kk,
            'rtList' => self::RT_LIST,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('kartukeluarga/edit/' . $id);
        }

        $kk = $this->model->find((int) $id);
        if (!$kk) {
            $this->redirect('kartukeluarga');
        }

        $alamat = trim($this->input('alamat', ''));
        $rtText = $this->input('rt', '');
        $rtId   = (int) $this->input('rt_id', 0);

        if ($alamat === '' || $rtText === '' || $rtId === 0) {
            setFlash('error', 'Alamat dan RT wajib diisi.');
            $this->redirect('kartukeluarga/edit/' . $id);
        }

        $this->model->update((int) $id, [
            'rt_id'   => $rtId,
            'alamat'  => $alamat,
            'rt_text' => $rtText,
        ]);

        $userId = $_SESSION['user']['id'] ?? null;
        $this->model->logRiwayat(
            (int) $id,
            'ubah_kk',
            null,
            "Data KK diperbarui (RT: {$rtText}, Alamat: {$alamat}).",
            $userId
        );

        setFlash('success', 'Data Kartu Keluarga berhasil diperbarui.');
        $this->redirect('kartukeluarga/show/' . $id);
    }

    // ──────────────────────────────────────────
    // Tambah Anggota KK
    // ──────────────────────────────────────────

    public function tambahAnggota(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        $kk = $this->model->findWithKepala((int) $id);
        if (!$kk) {
            $this->redirect('kartukeluarga');
        }

        $this->view('kartukeluarga/tambah-anggota', [
            'title'       => 'Tambah Anggota KK - ' . APP_NAME,
            'kk'          => $kk,
            'hubunganList'=> self::HUBUNGAN_LIST,
        ]);
    }

    public function storeAnggota(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('kartukeluarga/tambah-anggota/' . $id);
        }

        $kk = $this->model->find((int) $id);
        if (!$kk) {
            $this->redirect('kartukeluarga');
        }

        $hubungan = $this->input('hubungan', '');
        $nik      = trim($this->input('nik', ''));
        $nama     = trim($this->input('nama', ''));

        if ($hubungan === '') {
            setFlash('error', 'Hubungan dalam keluarga wajib dipilih.');
            $this->redirect('kartukeluarga/tambah-anggota/' . $id);
        }

        // Validate: only one Kepala Keluarga per KK
        if ($hubungan === 'Kepala Keluarga' && $this->model->hasKepalaKeluarga((int) $id)) {
            setFlash('error', 'KK ini sudah memiliki Kepala Keluarga.');
            $this->redirect('kartukeluarga/tambah-anggota/' . $id);
        }

        // Check if existing warga by NIK
        $wargaData = null;
        if ($nik !== '') {
            $wargaData = $this->wargaModel->findByNik($nik);
        }

        $userId = $_SESSION['user']['id'] ?? null;

        if ($wargaData) {
            // Existing warga: add to this KK
            $wargaId = (int) $wargaData['id'];
            // Update warga.kk_id
            $this->wargaModel->update($wargaId, ['kk_id' => (int) $id]);
            $this->model->addAnggota((int) $id, $wargaId, $hubungan);

            $this->model->logRiwayat(
                (int) $id, 'tambah_anggota', $wargaId,
                "Anggota {$wargaData['nama']} (NIK: {$nik}) ditambahkan sebagai {$hubungan}.",
                $userId
            );
        } else {
            // New warga
            if ($nik === '' || $nama === '') {
                setFlash('error', 'NIK dan Nama wajib diisi untuk anggota baru.');
                $this->redirect('kartukeluarga/tambah-anggota/' . $id);
            }

            if (!validateNik($nik)) {
                setFlash('error', 'NIK harus terdiri dari 16 digit angka.');
                $this->redirect('kartukeluarga/tambah-anggota/' . $id);
            }

            $wargaId = (int) $this->wargaModel->insert([
                'kk_id'          => (int) $id,
                'nik'            => $nik,
                'nama'           => $nama,
                'tempat_lahir'   => trim($this->input('tempat_lahir', '')),
                'tanggal_lahir'  => $this->input('tanggal_lahir', '') ?: null,
                'jenis_kelamin'  => $this->input('jenis_kelamin', '') ?: null,
                'agama'          => $this->input('agama', '') ?: null,
                'pendidikan'     => $this->input('pendidikan', '') ?: null,
                'pekerjaan'      => trim($this->input('pekerjaan', '')),
                'status_kawin'   => $this->input('status_kawin', '') ?: null,
                'status_warga'   => 'tetap',
            ]);

            $this->model->addAnggota((int) $id, $wargaId, $hubungan);

            $this->model->logRiwayat(
                (int) $id, 'tambah_anggota', $wargaId,
                "Anggota baru {$nama} (NIK: {$nik}) ditambahkan sebagai {$hubungan}.",
                $userId
            );
        }

        setFlash('success', 'Anggota berhasil ditambahkan ke Kartu Keluarga.');
        $this->redirect('kartukeluarga/show/' . $id);
    }

    // ──────────────────────────────────────────
    // Pindah KK
    // ──────────────────────────────────────────

    public function pindah(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        $kk      = $this->model->findWithKepala((int) $id);
        if (!$kk) {
            $this->redirect('kartukeluarga');
        }

        $anggota  = $this->model->getAnggota((int) $id);
        $allKk    = $this->model->listForDropdown();

        $this->view('kartukeluarga/pindah', [
            'title'        => 'Pindah KK - ' . APP_NAME,
            'kk'           => $kk,
            'anggota'      => $anggota,
            'allKk'        => $allKk,
            'hubunganList' => self::HUBUNGAN_LIST,
        ]);
    }

    public function prosesPindah(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('kartukeluarga/pindah/' . $id);
        }

        $wargaId  = (int) $this->input('warga_id', 0);
        $newKkId  = (int) $this->input('new_kk_id', 0);
        $hubungan = $this->input('hubungan', 'Lainnya');

        if ($wargaId === 0 || $newKkId === 0) {
            setFlash('error', 'Pilih anggota dan KK tujuan.');
            $this->redirect('kartukeluarga/pindah/' . $id);
        }

        if ($newKkId === (int) $id) {
            setFlash('error', 'KK tujuan tidak boleh sama dengan KK asal.');
            $this->redirect('kartukeluarga/pindah/' . $id);
        }

        $warga  = $this->wargaModel->find($wargaId);
        $newKk  = $this->model->find($newKkId);

        if (!$warga || !$newKk) {
            setFlash('error', 'Data tidak ditemukan.');
            $this->redirect('kartukeluarga/pindah/' . $id);
        }

        $ok = $this->model->pindahAnggota($wargaId, $newKkId, $hubungan);

        if (!$ok) {
            setFlash('error', 'Gagal memindahkan anggota. Silakan coba lagi.');
            $this->redirect('kartukeluarga/pindah/' . $id);
        }

        $userId = $_SESSION['user']['id'] ?? null;

        // Log di KK asal
        $this->model->logRiwayat(
            (int) $id, 'pindah_kk', $wargaId,
            "{$warga['nama']} pindah ke KK {$newKk['no_kk']} (RT {$newKk['rt_text']}).",
            $userId
        );

        // Log di KK tujuan
        $kkAsal = $this->model->find((int) $id);
        $this->model->logRiwayat(
            $newKkId, 'tambah_anggota', $wargaId,
            "{$warga['nama']} masuk dari KK " . ($kkAsal['no_kk'] ?? $id) . " sebagai {$hubungan}.",
            $userId
        );

        setFlash('success', "{$warga['nama']} berhasil dipindahkan ke KK {$newKk['no_kk']}.");
        $this->redirect('kartukeluarga/show/' . $id);
    }

    // ──────────────────────────────────────────
    // Cetak KK Internal
    // ──────────────────────────────────────────

    public function cetak(string $id): void
    {
        $this->requireAuth();

        $kk = $this->model->findWithKepala((int) $id);
        if (!$kk) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        $anggota = $this->model->getAnggota((int) $id);

        // Render without layout (print page)
        $this->view('kartukeluarga/cetak', [
            'title'   => 'Cetak KK - ' . $kk['no_kk'],
            'kk'      => $kk,
            'anggota' => $anggota,
        ], null);
    }

    // ──────────────────────────────────────────
    // API: Warga Lookup by NIK
    // ──────────────────────────────────────────

    public function apiWargaLookup(): void
    {
        $this->requireAuth();

        $nik    = trim($_GET['nik'] ?? '');
        $warga  = $nik !== '' ? $this->wargaModel->findByNik($nik) : false;

        $this->json($warga ?: null, $warga ? 200 : 404);
    }

    // ──────────────────────────────────────────
    // Riwayat Perubahan
    // ──────────────────────────────────────────

    public function riwayat(string $id): void
    {
        $this->requireAuth();

        $kk = $this->model->findWithKepala((int) $id);
        if (!$kk) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        $riwayat = $this->model->getRiwayat((int) $id);

        $this->view('kartukeluarga/riwayat', [
            'title'   => 'Riwayat KK ' . $kk['no_kk'] . ' - ' . APP_NAME,
            'kk'      => $kk,
            'riwayat' => $riwayat,
        ]);
    }
}
