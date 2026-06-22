<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/Tamu.php';
require_once APP_PATH . '/models/Kendaraan.php';
require_once APP_PATH . '/models/Insiden.php';
require_once APP_PATH . '/models/Patroli.php';
require_once APP_PATH . '/models/GatePass.php';
require_once APP_PATH . '/models/SecurityLog.php';

class SecurityController extends Controller
{
    private Tamu $tamu;
    private Kendaraan $kendaraan;
    private Insiden $insiden;
    private Patroli $patroli;
    private GatePass $gatePass;
    private SecurityLog $log;

    public function __construct()
    {
        $this->tamu = new Tamu();
        $this->kendaraan = new Kendaraan();
        $this->insiden = new Insiden();
        $this->patroli = new Patroli();
        $this->gatePass = new GatePass();
        $this->log = new SecurityLog();
    }

    public function index(): void
    {
        $this->requireAuth();

        $this->view('security/index', [
            'title' => 'Dashboard Security - ' . APP_NAME,
            'stats' => [
                'tamu_hari_ini' => $this->tamu->countToday(),
                'kendaraan_hari_ini' => $this->kendaraan->countToday(),
                'insiden_pending' => $this->insiden->countPending(),
            ],
            'aktivitasTerbaru' => $this->log->latest(10),
            'insidenPending' => $this->insiden->pending(),
            'patroliPerLokasi' => $this->patroli->summaryByLocation(7),
            'insidenPerTipe' => $this->insiden->summaryByType(7),
        ]);
    }

    public function tamu(): void
    {
        $this->requireAuth();
        $rt = (string) $this->query('rt', '');
        $rw = (string) $this->query('rw', '');

        $this->view('security/tamu', [
            'title' => 'Manajemen Tamu - ' . APP_NAME,
            'data' => $this->tamu->activeToday($rt, $rw),
            'filterRt' => $rt,
            'filterRw' => $rw,
        ]);
    }

    public function storeTamu(): void
    {
        $this->requireAuth();

        if (!$this->isPost() || !verifyCsrf()) {
            setFlash('error', 'Permintaan tidak valid.');
            $this->redirect('security/tamu');
        }

        $data = [
            'nama' => trim((string) $this->input('nama', '')),
            'no_identitas' => trim((string) $this->input('no_identitas', '')),
            'no_identitas_type' => (string) $this->input('no_identitas_type', 'KTP'),
            'no_telepon' => trim((string) $this->input('no_telepon', '')),
            'alamat_asal' => trim((string) $this->input('alamat_asal', '')),
            'keperluan' => trim((string) $this->input('keperluan', '')),
            'tanggal_kunjungan' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'nama_penerima' => trim((string) $this->input('nama_penerima', '')),
            'no_rumah' => trim((string) $this->input('no_rumah', '')),
            'rt' => trim((string) $this->input('rt', '')),
            'rw' => trim((string) $this->input('rw', '015')),
            'status_verifikasi' => 'pending',
            'catatan' => trim((string) $this->input('catatan', '')),
        ];

        if ($data['nama'] === '' || $data['no_identitas'] === '') {
            setFlash('error', 'Nama dan No. Identitas wajib diisi.');
            $this->redirect('security/tamu');
        }

        $tamuId = (int) $this->tamu->insert($data);

        $qrPayload = $this->gatePass->generatePayload($data['nama'], $data['no_identitas']);
        $this->gatePass->insert([
            'tamu_id' => $tamuId,
            'kendaraan_id' => null,
            'qr_code' => $qrPayload,
            'status' => 'aktif',
            'tanggal_berlaku' => date('Y-m-d'),
            'jam_berlaku_mulai' => date('H:i:s'),
            'jam_berlaku_selesai' => date('23:59:59'),
        ]);

        $this->log->add('check_in', (int) (authUser()['id'] ?? 0), $tamuId, 'Check-in tamu: ' . $data['nama']);

        setFlash('success', 'Check-in tamu berhasil dan QR Gate Pass sudah dibuat.');
        $this->redirect('security/tamu');
    }

    public function checkoutTamu(string $id): void
    {
        $this->requireAuth();
        if (!$this->isPost() || !verifyCsrf()) {
            setFlash('error', 'Permintaan tidak valid.');
            $this->redirect('security/tamu');
        }

        $this->tamu->checkout((int) $id);
        $this->log->add('check_out', (int) (authUser()['id'] ?? 0), (int) $id, 'Check-out tamu ID: ' . $id);

        setFlash('success', 'Check-out tamu berhasil.');
        $this->redirect('security/tamu');
    }

    public function kendaraan(): void
    {
        $this->requireAuth();
        $this->view('security/kendaraan', [
            'title' => 'Manajemen Kendaraan - ' . APP_NAME,
            'data' => $this->kendaraan->all('id', 'DESC'),
        ]);
    }

    public function storeKendaraan(): void
    {
        $this->requireAuth();
        if (!$this->isPost() || !verifyCsrf()) {
            setFlash('error', 'Permintaan tidak valid.');
            $this->redirect('security/kendaraan');
        }

        $data = [
            'no_plat' => strtoupper(trim((string) $this->input('no_plat', ''))),
            'jenis_kendaraan' => trim((string) $this->input('jenis_kendaraan', '')),
            'merk' => trim((string) $this->input('merk', '')),
            'warna' => trim((string) $this->input('warna', '')),
            'nama_pemilik' => trim((string) $this->input('nama_pemilik', '')),
            'no_telepon_pemilik' => trim((string) $this->input('no_telepon_pemilik', '')),
            'alamat_pemilik' => trim((string) $this->input('alamat_pemilik', '')),
            'keperluan' => trim((string) $this->input('keperluan', '')),
            'tanggal_masuk' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'lokasi_parkir' => trim((string) $this->input('lokasi_parkir', '')),
            'status' => 'parkir',
            'catatan' => trim((string) $this->input('catatan', '')),
        ];

        if ($data['no_plat'] === '') {
            setFlash('error', 'No. plat wajib diisi.');
            $this->redirect('security/kendaraan');
        }

        $id = (int) $this->kendaraan->insert($data);
        $this->log->add('check_in', (int) (authUser()['id'] ?? 0), $id, 'Kendaraan masuk: ' . $data['no_plat']);

        setFlash('success', 'Data kendaraan berhasil disimpan.');
        $this->redirect('security/kendaraan');
    }

    public function checkoutKendaraan(string $id): void
    {
        $this->requireAuth();
        if (!$this->isPost() || !verifyCsrf()) {
            setFlash('error', 'Permintaan tidak valid.');
            $this->redirect('security/kendaraan');
        }

        $this->kendaraan->checkout((int) $id);
        $this->log->add('check_out', (int) (authUser()['id'] ?? 0), (int) $id, 'Kendaraan checkout ID: ' . $id);

        setFlash('success', 'Check-out kendaraan berhasil.');
        $this->redirect('security/kendaraan');
    }

    public function insiden(): void
    {
        $this->requireAuth();
        $this->view('security/insiden', [
            'title' => 'Laporan Insiden - ' . APP_NAME,
            'data' => $this->insiden->all('id', 'DESC'),
        ]);
    }

    public function storeInsiden(): void
    {
        $this->requireAuth();
        if (!$this->isPost() || !verifyCsrf()) {
            setFlash('error', 'Permintaan tidak valid.');
            $this->redirect('security/insiden');
        }

        $data = [
            'tipe_insiden' => (string) $this->input('tipe_insiden', 'kejadian_lain'),
            'lokasi' => trim((string) $this->input('lokasi', '')),
            'tanggal_insiden' => date('Y-m-d'),
            'jam_insiden' => date('H:i:s'),
            'deskripsi' => trim((string) $this->input('deskripsi', '')),
            'nama_pelapor' => trim((string) $this->input('nama_pelapor', '')),
            'no_telepon_pelapor' => trim((string) $this->input('no_telepon_pelapor', '')),
            'status' => 'baru',
        ];

        if ($data['deskripsi'] === '') {
            setFlash('error', 'Deskripsi insiden wajib diisi.');
            $this->redirect('security/insiden');
        }

        $id = (int) $this->insiden->insert($data);
        $this->log->add('insiden', (int) (authUser()['id'] ?? 0), $id, 'Insiden baru dilaporkan.');

        setFlash('success', 'Insiden berhasil dilaporkan.');
        $this->redirect('security/insiden');
    }

    public function updateInsidenStatus(string $id): void
    {
        $this->requireAuth();
        if (!$this->isPost() || !verifyCsrf()) {
            setFlash('error', 'Permintaan tidak valid.');
            $this->redirect('security/insiden');
        }

        $status = (string) $this->input('status', 'baru');
        if (!in_array($status, ['baru', 'diproses', 'selesai'], true)) {
            $status = 'baru';
        }

        $this->insiden->updateStatus(
            (int) $id,
            $status,
            trim((string) $this->input('petugas_penangani', '')),
            trim((string) $this->input('catatan_penanganan', ''))
        );

        $this->log->add('insiden', (int) (authUser()['id'] ?? 0), (int) $id, 'Status insiden diperbarui: ' . $status);

        setFlash('success', 'Status insiden berhasil diperbarui.');
        $this->redirect('security/insiden');
    }

    public function patroli(): void
    {
        $this->requireAuth();
        $this->view('security/patroli', [
            'title' => 'Riwayat Patroli - ' . APP_NAME,
            'data' => $this->patroli->latest(100),
            'summaryLokasi' => $this->patroli->summaryByLocation(30),
        ]);
    }

    public function storePatroli(): void
    {
        $this->requireAuth();
        if (!$this->isPost() || !verifyCsrf()) {
            setFlash('error', 'Permintaan tidak valid.');
            $this->redirect('security/patroli');
        }

        $data = [
            'tanggal_patroli' => date('Y-m-d'),
            'jam_patroli' => date('H:i:s'),
            'petugas_patroli' => trim((string) $this->input('petugas_patroli', '')),
            'lokasi_patroli' => trim((string) $this->input('lokasi_patroli', '')),
            'status_kondisi' => (string) $this->input('status_kondisi', 'aman'),
            'catatan' => trim((string) $this->input('catatan', '')),
        ];

        if ($data['petugas_patroli'] === '' || $data['lokasi_patroli'] === '') {
            setFlash('error', 'Petugas dan lokasi patroli wajib diisi.');
            $this->redirect('security/patroli');
        }

        $id = (int) $this->patroli->insert($data);
        $this->log->add('patroli', (int) (authUser()['id'] ?? 0), $id, 'Check-in patroli di ' . $data['lokasi_patroli']);

        setFlash('success', 'Data patroli berhasil disimpan.');
        $this->redirect('security/patroli');
    }

    public function qrPass(): void
    {
        $this->requireAuth();
        $this->view('security/qr-pass', [
            'title' => 'QR Gate Pass - ' . APP_NAME,
            'data' => $this->gatePass->all('id', 'DESC'),
        ]);
    }

    public function checkInOut(): void
    {
        $this->requireAuth();
        $this->view('security/check-in-out', [
            'title' => 'Check-In / Check-Out - ' . APP_NAME,
            'pendingTamu' => $this->tamu->pendingCheckout(),
            'kendaraanParkir' => $this->kendaraan->parkedNow(),
        ]);
    }

    public function laporan(): void
    {
        $this->requireAuth();

        $this->view('security/laporan', [
            'title' => 'Laporan Security - ' . APP_NAME,
            'daily' => [
                'tamu' => $this->tamu->countToday(),
                'kendaraan' => $this->kendaraan->countToday(),
                'insiden' => $this->insiden->countPending(),
                'patroli' => count($this->patroli->latest(100)),
            ],
            'insidenPerTipe' => $this->insiden->summaryByType(30),
            'patroliPerLokasi' => $this->patroli->summaryByLocation(30),
        ]);
    }
}
