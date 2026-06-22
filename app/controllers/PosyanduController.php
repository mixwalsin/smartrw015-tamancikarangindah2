<?php

/**
 * PosyanduController
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Handles: Dashboard, Balita, Ibu Hamil, Jadwal, Imunisasi, Timbangan, Grafik
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/BalitaModel.php';
require_once APP_PATH . '/models/IbuHamilModel.php';
require_once APP_PATH . '/models/JadwalPosyanduModel.php';
require_once APP_PATH . '/models/ImunisasiModel.php';
require_once APP_PATH . '/models/TimbanganModel.php';

class PosyanduController extends Controller
{
    private BalitaModel        $balitaModel;
    private IbuHamilModel      $ibuHamilModel;
    private JadwalPosyanduModel $jadwalModel;
    private ImunisasiModel     $imunisasiModel;
    private TimbanganModel     $timbanganModel;

    public function __construct()
    {
        $this->balitaModel    = new BalitaModel();
        $this->ibuHamilModel  = new IbuHamilModel();
        $this->jadwalModel    = new JadwalPosyanduModel();
        $this->imunisasiModel = new ImunisasiModel();
        $this->timbanganModel = new TimbanganModel();
    }

    // ──────────────────────────────────────────
    // DASHBOARD
    // ──────────────────────────────────────────

    public function index(): void
    {
        $this->requireAuth();
        $this->view('posyandu/index', [
            'title'           => 'Posyandu - ' . APP_NAME,
            'totalBalita'     => $this->balitaModel->count(),
            'totalIbuHamil'   => $this->ibuHamilModel->count(),
            'jadwalBulanIni'  => $this->jadwalModel->countBulanIni(),
            'giziKurang'      => $this->timbanganModel->countGiziKurang(),
            'berisiko'        => $this->ibuHamilModel->countBerisiko(),
            'jadwalMendatang' => $this->jadwalModel->getMendatang(),
        ]);
    }

    // ──────────────────────────────────────────
    // BALITA
    // ──────────────────────────────────────────

    public function balitaIndex(): void
    {
        $this->requireAuth();
        $page    = (int) $this->query('page', 1);
        $keyword = trim($this->query('keyword', ''));
        $this->view('posyandu/balita/index', [
            'title'      => 'Data Balita - ' . APP_NAME,
            'pagination' => $this->balitaModel->paginateWithSearch($page, $keyword),
        ]);
    }

    public function balitaCreate(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');
        $this->view('posyandu/balita/create', [
            'title' => 'Tambah Balita - ' . APP_NAME,
        ]);
    }

    public function balitaStore(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/balita/create');
        }

        $nama = trim($this->input('nama', ''));
        if ($nama === '') {
            setFlash('error', 'Nama balita wajib diisi.');
            $this->redirect('posyandu/balita/create');
        }

        $berat   = $this->input('berat_badan', '');
        $tinggi  = $this->input('tinggi_badan', '');

        $data = [
            'nama'             => $nama,
            'jenis_kelamin'    => $this->input('jenis_kelamin', 'L'),
            'tgl_lahir'        => $this->input('tgl_lahir', ''),
            'nama_ibu'         => trim($this->input('nama_ibu', '')),
            'nama_ayah'        => trim($this->input('nama_ayah', '')),
            'alamat'           => trim($this->input('alamat', '')),
            'no_rumah'         => trim($this->input('no_rumah', '')),
            'rt'               => $this->input('rt', '007'),
            'rw'               => '015',
            'berat_badan'      => $berat !== '' ? (float) $berat : null,
            'tinggi_badan'     => $tinggi !== '' ? (float) $tinggi : null,
            'status_imunisasi' => $this->input('status_imunisasi', 'belum'),
            'catatan'          => trim($this->input('catatan', '')),
        ];

        $this->balitaModel->insert($data);
        setFlash('success', 'Data balita berhasil ditambahkan.');
        $this->redirect('posyandu/balita');
    }

    public function balitaShow(string $id): void
    {
        $this->requireAuth();
        $balita = $this->balitaModel->find((int) $id);
        if (!$balita) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }
        $imunisasi  = $this->imunisasiModel->getByBalita((int) $id);
        $timbangan  = $this->timbanganModel->getByBalita((int) $id);
        $this->view('posyandu/balita/show', [
            'title'     => 'Detail Balita - ' . APP_NAME,
            'balita'    => $balita,
            'imunisasi' => $imunisasi,
            'timbangan' => $timbangan,
        ]);
    }

    public function balitaEdit(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');
        $balita = $this->balitaModel->find((int) $id);
        if (!$balita) {
            $this->redirect('posyandu/balita');
        }
        $this->view('posyandu/balita/edit', [
            'title'  => 'Edit Balita - ' . APP_NAME,
            'balita' => $balita,
        ]);
    }

    public function balitaUpdate(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/balita/edit/' . $id);
        }

        $berat  = $this->input('berat_badan', '');
        $tinggi = $this->input('tinggi_badan', '');

        $data = [
            'nama'             => trim($this->input('nama', '')),
            'jenis_kelamin'    => $this->input('jenis_kelamin', 'L'),
            'tgl_lahir'        => $this->input('tgl_lahir', ''),
            'nama_ibu'         => trim($this->input('nama_ibu', '')),
            'nama_ayah'        => trim($this->input('nama_ayah', '')),
            'alamat'           => trim($this->input('alamat', '')),
            'no_rumah'         => trim($this->input('no_rumah', '')),
            'rt'               => $this->input('rt', '007'),
            'berat_badan'      => $berat !== '' ? (float) $berat : null,
            'tinggi_badan'     => $tinggi !== '' ? (float) $tinggi : null,
            'status_imunisasi' => $this->input('status_imunisasi', 'belum'),
            'catatan'          => trim($this->input('catatan', '')),
        ];

        $this->balitaModel->update((int) $id, $data);
        setFlash('success', 'Data balita berhasil diperbarui.');
        $this->redirect('posyandu/balita');
    }

    public function balitaDelete(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/balita');
        }

        $this->balitaModel->delete((int) $id);
        setFlash('success', 'Data balita berhasil dihapus.');
        $this->redirect('posyandu/balita');
    }

    // ──────────────────────────────────────────
    // IBU HAMIL
    // ──────────────────────────────────────────

    public function ibuHamilIndex(): void
    {
        $this->requireAuth();
        $page    = (int) $this->query('page', 1);
        $keyword = trim($this->query('keyword', ''));
        $this->view('posyandu/ibu_hamil/index', [
            'title'      => 'Data Ibu Hamil - ' . APP_NAME,
            'pagination' => $this->ibuHamilModel->paginateWithSearch($page, $keyword),
        ]);
    }

    public function ibuHamilCreate(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');
        $this->view('posyandu/ibu_hamil/create', [
            'title' => 'Tambah Ibu Hamil - ' . APP_NAME,
        ]);
    }

    public function ibuHamilStore(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/ibu-hamil/create');
        }

        $nama = trim($this->input('nama', ''));
        if ($nama === '') {
            setFlash('error', 'Nama ibu hamil wajib diisi.');
            $this->redirect('posyandu/ibu-hamil/create');
        }

        $data = [
            'nama'                => $nama,
            'umur'                => (int) $this->input('umur', 0),
            'alamat'              => trim($this->input('alamat', '')),
            'no_rumah'            => trim($this->input('no_rumah', '')),
            'rt'                  => $this->input('rt', '007'),
            'rw'                  => '015',
            'tgl_perkiraan_lahir' => $this->input('tgl_perkiraan_lahir', '') ?: null,
            'bulan_kehamilan'     => (int) $this->input('bulan_kehamilan', 1),
            'status_kesehatan'    => $this->input('status_kesehatan', 'normal'),
            'catatan'             => trim($this->input('catatan', '')),
        ];

        $this->ibuHamilModel->insert($data);
        setFlash('success', 'Data ibu hamil berhasil ditambahkan.');
        $this->redirect('posyandu/ibu-hamil');
    }

    public function ibuHamilShow(string $id): void
    {
        $this->requireAuth();
        $ibuHamil = $this->ibuHamilModel->find((int) $id);
        if (!$ibuHamil) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }
        $this->view('posyandu/ibu_hamil/show', [
            'title'    => 'Detail Ibu Hamil - ' . APP_NAME,
            'ibuHamil' => $ibuHamil,
        ]);
    }

    public function ibuHamilEdit(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');
        $ibuHamil = $this->ibuHamilModel->find((int) $id);
        if (!$ibuHamil) {
            $this->redirect('posyandu/ibu-hamil');
        }
        $this->view('posyandu/ibu_hamil/edit', [
            'title'    => 'Edit Ibu Hamil - ' . APP_NAME,
            'ibuHamil' => $ibuHamil,
        ]);
    }

    public function ibuHamilUpdate(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/ibu-hamil/edit/' . $id);
        }

        $data = [
            'nama'                => trim($this->input('nama', '')),
            'umur'                => (int) $this->input('umur', 0),
            'alamat'              => trim($this->input('alamat', '')),
            'no_rumah'            => trim($this->input('no_rumah', '')),
            'rt'                  => $this->input('rt', '007'),
            'tgl_perkiraan_lahir' => $this->input('tgl_perkiraan_lahir', '') ?: null,
            'bulan_kehamilan'     => (int) $this->input('bulan_kehamilan', 1),
            'status_kesehatan'    => $this->input('status_kesehatan', 'normal'),
            'catatan'             => trim($this->input('catatan', '')),
        ];

        $this->ibuHamilModel->update((int) $id, $data);
        setFlash('success', 'Data ibu hamil berhasil diperbarui.');
        $this->redirect('posyandu/ibu-hamil');
    }

    public function ibuHamilDelete(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/ibu-hamil');
        }

        $this->ibuHamilModel->delete((int) $id);
        setFlash('success', 'Data ibu hamil berhasil dihapus.');
        $this->redirect('posyandu/ibu-hamil');
    }

    // ──────────────────────────────────────────
    // JADWAL POSYANDU
    // ──────────────────────────────────────────

    public function jadwalIndex(): void
    {
        $this->requireAuth();
        $page = (int) $this->query('page', 1);
        $this->view('posyandu/jadwal/index', [
            'title'      => 'Jadwal Posyandu - ' . APP_NAME,
            'pagination' => $this->jadwalModel->paginate($page),
        ]);
    }

    public function jadwalCreate(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');
        $this->view('posyandu/jadwal/create', [
            'title' => 'Buat Jadwal Posyandu - ' . APP_NAME,
        ]);
    }

    public function jadwalStore(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/jadwal/create');
        }

        $data = [
            'tanggal'     => $this->input('tanggal', ''),
            'jam_mulai'   => $this->input('jam_mulai', '08:00'),
            'jam_selesai' => $this->input('jam_selesai', '12:00'),
            'lokasi'      => trim($this->input('lokasi', '')),
            'keterangan'  => trim($this->input('keterangan', '')),
            'status'      => $this->input('status', 'dijadwalkan'),
        ];

        if ($data['tanggal'] === '' || $data['lokasi'] === '') {
            setFlash('error', 'Tanggal dan lokasi wajib diisi.');
            $this->redirect('posyandu/jadwal/create');
        }

        $this->jadwalModel->insert($data);
        setFlash('success', 'Jadwal posyandu berhasil dibuat.');
        $this->redirect('posyandu/jadwal');
    }

    public function jadwalEdit(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');
        $jadwal = $this->jadwalModel->find((int) $id);
        if (!$jadwal) {
            $this->redirect('posyandu/jadwal');
        }
        $this->view('posyandu/jadwal/edit', [
            'title'  => 'Edit Jadwal - ' . APP_NAME,
            'jadwal' => $jadwal,
        ]);
    }

    public function jadwalUpdate(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/jadwal/edit/' . $id);
        }

        $data = [
            'tanggal'     => $this->input('tanggal', ''),
            'jam_mulai'   => $this->input('jam_mulai', '08:00'),
            'jam_selesai' => $this->input('jam_selesai', '12:00'),
            'lokasi'      => trim($this->input('lokasi', '')),
            'keterangan'  => trim($this->input('keterangan', '')),
            'status'      => $this->input('status', 'dijadwalkan'),
        ];

        $this->jadwalModel->update((int) $id, $data);
        setFlash('success', 'Jadwal posyandu berhasil diperbarui.');
        $this->redirect('posyandu/jadwal');
    }

    public function jadwalDelete(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/jadwal');
        }

        $this->jadwalModel->delete((int) $id);
        setFlash('success', 'Jadwal posyandu berhasil dihapus.');
        $this->redirect('posyandu/jadwal');
    }

    // ──────────────────────────────────────────
    // IMUNISASI
    // ──────────────────────────────────────────

    public function imunisasiIndex(): void
    {
        $this->requireAuth();
        $page    = (int) $this->query('page', 1);
        $balitas = $this->balitaModel->all('nama');
        $this->view('posyandu/imunisasi/index', [
            'title'      => 'Data Imunisasi - ' . APP_NAME,
            'pagination' => $this->imunisasiModel->allWithBalita($page),
            'balitas'    => $balitas,
        ]);
    }

    public function imunisasiCreate(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');
        $balitaId = (int) $this->query('balita_id', 0);
        $this->view('posyandu/imunisasi/create', [
            'title'           => 'Tambah Imunisasi - ' . APP_NAME,
            'balitas'         => $this->balitaModel->all('nama'),
            'selectedBalitaId'=> $balitaId,
        ]);
    }

    public function imunisasiStore(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/imunisasi/create');
        }

        $balitaId = (int) $this->input('balita_id', 0);
        $jenis    = trim($this->input('jenis_imunisasi', ''));

        if ($balitaId === 0 || $jenis === '') {
            setFlash('error', 'Balita dan jenis imunisasi wajib diisi.');
            $this->redirect('posyandu/imunisasi/create');
        }

        $data = [
            'balita_id'         => $balitaId,
            'jenis_imunisasi'   => $jenis,
            'tanggal_imunisasi' => $this->input('tanggal_imunisasi', date('Y-m-d')),
            'tempat_imunisasi'  => trim($this->input('tempat_imunisasi', '')),
            'catatan'           => trim($this->input('catatan', '')),
        ];

        $this->imunisasiModel->insert($data);

        // Perbarui status imunisasi di tabel balita
        $semuaImunisasi = $this->imunisasiModel->getByBalita($balitaId);
        $jenisWajib     = ['HB-0','BCG','DPT-HB-Hib 1','DPT-HB-Hib 2','DPT-HB-Hib 3','Polio 1','Polio 2','Polio 3','Polio 4','IPV','Campak/MR'];
        $done           = array_column($semuaImunisasi, 'jenis_imunisasi');
        $lengkap        = count(array_intersect($jenisWajib, $done)) === count($jenisWajib);
        $this->balitaModel->update($balitaId, [
            'status_imunisasi' => $lengkap ? 'lengkap' : 'tidak_lengkap',
        ]);

        setFlash('success', 'Data imunisasi berhasil disimpan.');
        $this->redirect('posyandu/imunisasi');
    }

    public function imunisasiDelete(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/imunisasi');
        }

        $this->imunisasiModel->delete((int) $id);
        setFlash('success', 'Data imunisasi berhasil dihapus.');
        $this->redirect('posyandu/imunisasi');
    }

    // ──────────────────────────────────────────
    // TIMBANGAN
    // ──────────────────────────────────────────

    public function timbanganIndex(): void
    {
        $this->requireAuth();
        $page = (int) $this->query('page', 1);
        $this->view('posyandu/timbangan/index', [
            'title'      => 'Data Penimbangan - ' . APP_NAME,
            'pagination' => $this->timbanganModel->allWithBalita($page),
            'balitas'    => $this->balitaModel->all('nama'),
        ]);
    }

    public function timbanganCreate(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');
        $balitaId = (int) $this->query('balita_id', 0);
        $this->view('posyandu/timbangan/create', [
            'title'           => 'Tambah Penimbangan - ' . APP_NAME,
            'balitas'         => $this->balitaModel->getWithUmur(),
            'selectedBalitaId'=> $balitaId,
        ]);
    }

    public function timbanganStore(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/timbangan/create');
        }

        $balitaId = (int) $this->input('balita_id', 0);
        $berat    = (float) $this->input('berat_badan', 0);

        if ($balitaId === 0 || $berat <= 0) {
            setFlash('error', 'Balita dan berat badan wajib diisi.');
            $this->redirect('posyandu/timbangan/create');
        }

        // Hitung status gizi otomatis
        $balita     = $this->balitaModel->find($balitaId);
        $statusGizi = $this->input('status_gizi', 'gizi_baik');

        if ($balita) {
            $tglLahir  = new \DateTime($balita['tgl_lahir']);
            $sekarang  = new \DateTime();
            $diff      = $tglLahir->diff($sekarang);
            $usiaBulan = $diff->y * 12 + $diff->m;
            $statusGizi = TimbanganModel::hitungStatusGizi(
                $berat,
                $usiaBulan,
                $balita['jenis_kelamin']
            );
        }

        $tinggi = $this->input('tinggi_badan', '');

        $data = [
            'balita_id'       => $balitaId,
            'tanggal_timbang' => $this->input('tanggal_timbang', date('Y-m-d')),
            'berat_badan'     => $berat,
            'tinggi_badan'    => $tinggi !== '' ? (float) $tinggi : null,
            'status_gizi'     => $statusGizi,
            'catatan'         => trim($this->input('catatan', '')),
        ];

        $this->timbanganModel->insert($data);

        // Update berat/tinggi terbaru di data balita
        $updateBalita = ['berat_badan' => $berat];
        if ($tinggi !== '') {
            $updateBalita['tinggi_badan'] = (float) $tinggi;
        }
        $this->balitaModel->update($balitaId, $updateBalita);

        if (in_array($statusGizi, ['gizi_kurang', 'gizi_buruk'], true)) {
            setFlash('warning', 'Data timbangan disimpan. Perhatian: status gizi ' . str_replace('_', ' ', $statusGizi) . '!');
        } else {
            setFlash('success', 'Data timbangan berhasil disimpan.');
        }
        $this->redirect('posyandu/timbangan');
    }

    public function timbanganDelete(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'petugas_posyandu');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('posyandu/timbangan');
        }

        $this->timbanganModel->delete((int) $id);
        setFlash('success', 'Data timbangan berhasil dihapus.');
        $this->redirect('posyandu/timbangan');
    }

    // ──────────────────────────────────────────
    // GRAFIK PERTUMBUHAN
    // ──────────────────────────────────────────

    public function grafikIndex(): void
    {
        $this->requireAuth();
        $balitas = $this->balitaModel->getWithUmur();
        $this->view('posyandu/grafik/index', [
            'title'   => 'Grafik Pertumbuhan - ' . APP_NAME,
            'balitas' => $balitas,
        ]);
    }

    public function grafikBalita(string $balitaId): void
    {
        $this->requireAuth();
        $balita = $this->balitaModel->find((int) $balitaId);
        if (!$balita) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        $timbangan = $this->timbanganModel->getByBalita((int) $balitaId);

        // Siapkan data untuk Chart.js
        $labels   = [];
        $berats   = [];
        $tinggis  = [];
        foreach ($timbangan as $t) {
            $labels[]  = date('d/m/Y', strtotime($t['tanggal_timbang']));
            $berats[]  = (float) $t['berat_badan'];
            $tinggis[] = $t['tinggi_badan'] !== null ? (float) $t['tinggi_badan'] : null;
        }

        $this->view('posyandu/grafik/show', [
            'title'     => 'Grafik Pertumbuhan - ' . APP_NAME,
            'balita'    => $balita,
            'timbangan' => $timbangan,
            'labels'    => json_encode($labels, JSON_UNESCAPED_UNICODE),
            'berats'    => json_encode($berats),
            'tinggis'   => json_encode($tinggis),
        ]);
    }
}
