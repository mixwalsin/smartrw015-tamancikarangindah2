<?php

/**
 * SuratController - Sistem Surat Online
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Workflow: Draft → Menunggu RT → Menunggu RW → Disetujui/Ditolak → Selesai
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/SuratModel.php';
require_once APP_PATH . '/models/PengajuanSuratModel.php';
require_once APP_PATH . '/models/SuratHistoryModel.php';

class SuratController extends Controller
{
    private SuratModel          $suratModel;
    private PengajuanSuratModel $pengajuanModel;
    private SuratHistoryModel   $historyModel;

    public function __construct()
    {
        $this->suratModel     = new SuratModel();
        $this->pengajuanModel = new PengajuanSuratModel();
        $this->historyModel   = new SuratHistoryModel();
    }

    // ──────────────────────────────────────────
    // index - Daftar Pengajuan
    // ──────────────────────────────────────────

    public function index(): void
    {
        $this->requireAuth();
        $user = authUser();

        $page    = (int) ($this->query('page', 1));
        $status  = $this->query('status', '');
        $keyword = trim($this->query('keyword', ''));

        // Warga hanya lihat milik sendiri; RT/RW/admin lihat semua
        $dibuatOleh = 0;
        $rt         = '';
        if ($user['role'] === 'warga') {
            $dibuatOleh = (int) $user['id'];
        }

        $pagination   = $this->pengajuanModel->paginateWithFilter(
            $page, $status, $keyword, $dibuatOleh, $rt
        );
        $statusCounts = $this->pengajuanModel->countByStatus();

        $this->view('surat/index', [
            'title'        => 'Surat Menyurat - ' . APP_NAME,
            'pagination'   => $pagination,
            'statusCounts' => $statusCounts,
            'filterStatus' => $status,
            'keyword'      => $keyword,
        ]);
    }

    // ──────────────────────────────────────────
    // create - Form Pengajuan Surat
    // ──────────────────────────────────────────

    public function create(): void
    {
        $this->requireAuth();
        $jenisList = $this->suratModel->getActive();
        $jenis     = null;
        $jenisId   = (int) $this->query('jenis', 0);
        if ($jenisId > 0) {
            $jenis = $this->suratModel->find($jenisId);
        }

        $this->view('surat/create', [
            'title'     => 'Ajukan Surat - ' . APP_NAME,
            'jenisList' => $jenisList,
            'jenis'     => $jenis,
        ]);
    }

    // ──────────────────────────────────────────
    // store - Simpan Pengajuan
    // ──────────────────────────────────────────

    public function store(): void
    {
        $this->requireAuth();

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('surat/create');
        }

        $user    = authUser();
        $jenisId = (int) $this->input('jenis_id', 0);
        $jenis   = $jenisId > 0 ? $this->suratModel->find($jenisId) : null;

        if (!$jenis) {
            setFlash('error', 'Jenis surat tidak valid.');
            $this->redirect('surat/create');
        }

        // Validasi field wajib
        $nama     = trim($this->input('pemohon_nama', ''));
        $nik      = trim($this->input('pemohon_nik', ''));
        $alamat   = trim($this->input('pemohon_alamat', ''));
        $rt       = $this->input('pemohon_rt', '001');
        $keperluan = trim($this->input('keperluan', ''));

        if ($nama === '' || $nik === '' || $alamat === '' || $keperluan === '') {
            setFlash('error', 'Nama, NIK, Alamat, dan Keperluan wajib diisi.');
            $this->redirect('surat/create?jenis=' . $jenisId);
        }

        if (!preg_match('/^\d{16}$/', $nik)) {
            setFlash('error', 'NIK harus 16 digit angka.');
            $this->redirect('surat/create?jenis=' . $jenisId);
        }

        // Handle lampiran upload
        $lampiranPath = null;
        if (!empty($_FILES['lampiran']['name'])) {
            try {
                $lampiranPath = uploadFile($_FILES['lampiran'], 'surat');
            } catch (RuntimeException $e) {
                setFlash('error', 'Upload lampiran gagal: ' . $e->getMessage());
                $this->redirect('surat/create?jenis=' . $jenisId);
            }
        }

        $data = [
            'jenis_id'             => $jenisId,
            'kode_verifikasi'      => $this->pengajuanModel->generateKodeVerifikasi(),
            'pemohon_nama'         => $nama,
            'pemohon_nik'          => $nik,
            'pemohon_tempat_lahir' => trim($this->input('pemohon_tempat_lahir', '')),
            'pemohon_tgl_lahir'    => $this->input('pemohon_tgl_lahir', '') ?: null,
            'pemohon_jk'           => $this->input('pemohon_jk', '') ?: null,
            'pemohon_alamat'       => $alamat,
            'pemohon_rt'           => $rt,
            'pemohon_agama'        => $this->input('pemohon_agama', '') ?: null,
            'pemohon_pekerjaan'    => trim($this->input('pemohon_pekerjaan', '')) ?: null,
            'pemohon_no_hp'        => trim($this->input('pemohon_no_hp', '')) ?: null,
            'keperluan'            => $keperluan,
            'keterangan_tambahan'  => trim($this->input('keterangan_tambahan', '')) ?: null,
            'lampiran'             => $lampiranPath,
            'status'               => PengajuanSuratModel::STATUS_MENUNGGU_RT,
            'dibuat_oleh'          => (int) $user['id'],
            'dibuat_at'            => date('Y-m-d H:i:s'),
            'rt_status'            => 'menunggu',
            'rw_status'            => 'menunggu',
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ];

        $id = $this->pengajuanModel->insertRaw($data);

        if ($id) {
            // Catat history
            $this->historyModel->insert([
                'pengajuan_id'   => (int) $id,
                'status_lama'    => 'baru',
                'status_baru'    => PengajuanSuratModel::STATUS_MENUNGGU_RT,
                'catatan'        => 'Pengajuan surat baru disubmit oleh warga',
                'dilakukan_oleh' => (int) $user['id'],
            ]);

            setFlash('success', 'Pengajuan surat berhasil dikirim. Menunggu verifikasi RT.');
            $this->redirect('surat/show/' . $id);
        }

        setFlash('error', 'Gagal menyimpan pengajuan. Silakan coba lagi.');
        $this->redirect('surat/create?jenis=' . $jenisId);
    }

    // ──────────────────────────────────────────
    // show - Detail Pengajuan
    // ──────────────────────────────────────────

    public function show(string $id): void
    {
        $this->requireAuth();
        $user     = authUser();
        $pengajuan = $this->pengajuanModel->findWithDetail((int) $id);

        if (!$pengajuan) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        // Warga hanya bisa lihat miliknya sendiri
        if ($user['role'] === 'warga' && (int) $pengajuan['dibuat_oleh'] !== (int) $user['id']) {
            http_response_code(403);
            $this->view('errors/403', ['title' => '403 - Akses Ditolak']);
            return;
        }

        $history = $this->historyModel->getByPengajuan((int) $id);

        $this->view('surat/show', [
            'title'     => 'Detail Surat - ' . APP_NAME,
            'pengajuan' => $pengajuan,
            'history'   => $history,
        ]);
    }

    // ──────────────────────────────────────────
    // verifyRt - RT Verifikasi Surat
    // ──────────────────────────────────────────

    public function verifyRt(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('surat/show/' . $id);
        }

        $user     = authUser();
        $pengajuan = $this->pengajuanModel->find((int) $id);

        if (!$pengajuan || $pengajuan['status'] !== PengajuanSuratModel::STATUS_MENUNGGU_RT) {
            setFlash('error', 'Pengajuan tidak ditemukan atau status tidak sesuai.');
            $this->redirect('surat');
        }

        $action  = $this->input('action', 'verify');
        $catatan = trim($this->input('catatan', ''));
        $now     = date('Y-m-d H:i:s');

        if ($action === 'verify') {
            $extraData = [
                'status'              => PengajuanSuratModel::STATUS_MENUNGGU_RW,
                'rt_verifikasi_oleh'  => (int) $user['id'],
                'rt_verifikasi_at'    => $now,
                'rt_catatan'          => $catatan,
                'rt_status'           => 'diverifikasi',
                'updated_at'          => $now,
            ];
            $this->pengajuanModel->update((int) $id, $extraData);
            $this->historyModel->insert([
                'pengajuan_id'   => (int) $id,
                'status_lama'    => PengajuanSuratModel::STATUS_MENUNGGU_RT,
                'status_baru'    => PengajuanSuratModel::STATUS_MENUNGGU_RW,
                'catatan'        => $catatan ?: 'Diverifikasi oleh RT',
                'dilakukan_oleh' => (int) $user['id'],
            ]);
            setFlash('success', 'Surat berhasil diverifikasi oleh RT. Menunggu persetujuan RW.');
        } else {
            // Tolak
            $extraData = [
                'status'             => PengajuanSuratModel::STATUS_DITOLAK,
                'rt_verifikasi_oleh' => (int) $user['id'],
                'rt_verifikasi_at'   => $now,
                'rt_catatan'         => $catatan,
                'rt_status'          => 'ditolak',
                'updated_at'         => $now,
            ];
            $this->pengajuanModel->update((int) $id, $extraData);
            $this->historyModel->insert([
                'pengajuan_id'   => (int) $id,
                'status_lama'    => PengajuanSuratModel::STATUS_MENUNGGU_RT,
                'status_baru'    => PengajuanSuratModel::STATUS_DITOLAK,
                'catatan'        => $catatan ?: 'Ditolak oleh RT',
                'dilakukan_oleh' => (int) $user['id'],
            ]);
            setFlash('warning', 'Pengajuan surat ditolak oleh RT.');
        }

        $this->redirect('surat/show/' . $id);
    }

    // ──────────────────────────────────────────
    // approve - RW Approval Surat
    // ──────────────────────────────────────────

    public function approve(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('surat/show/' . $id);
        }

        $user      = authUser();
        $pengajuan  = $this->pengajuanModel->findWithDetail((int) $id);

        if (!$pengajuan || $pengajuan['status'] !== PengajuanSuratModel::STATUS_MENUNGGU_RW) {
            setFlash('error', 'Pengajuan tidak ditemukan atau status tidak sesuai.');
            $this->redirect('surat');
        }

        $catatan = trim($this->input('catatan', ''));
        $now     = date('Y-m-d H:i:s');

        // Generate nomor surat
        $noSurat = $this->pengajuanModel->generateNoSurat($pengajuan['jenis_kode']);

        $extraData = [
            'status'            => PengajuanSuratModel::STATUS_DISETUJUI,
            'no_surat'          => $noSurat,
            'rw_approval_oleh'  => (int) $user['id'],
            'rw_approval_at'    => $now,
            'rw_catatan'        => $catatan,
            'rw_status'         => 'disetujui',
            'updated_at'        => $now,
        ];
        $this->pengajuanModel->update((int) $id, $extraData);
        $this->historyModel->insert([
            'pengajuan_id'   => (int) $id,
            'status_lama'    => PengajuanSuratModel::STATUS_MENUNGGU_RW,
            'status_baru'    => PengajuanSuratModel::STATUS_DISETUJUI,
            'catatan'        => $catatan ?: 'Disetujui oleh RW',
            'dilakukan_oleh' => (int) $user['id'],
        ]);

        setFlash('success', 'Surat berhasil disetujui. No. Surat: ' . $noSurat);
        $this->redirect('surat/show/' . $id);
    }

    // ──────────────────────────────────────────
    // reject - Tolak oleh RW
    // ──────────────────────────────────────────

    public function reject(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('surat/show/' . $id);
        }

        $user     = authUser();
        $pengajuan = $this->pengajuanModel->find((int) $id);

        if (!$pengajuan || !in_array($pengajuan['status'], [
            PengajuanSuratModel::STATUS_MENUNGGU_RW,
            PengajuanSuratModel::STATUS_MENUNGGU_RT,
        ], true)) {
            setFlash('error', 'Pengajuan tidak ditemukan atau status tidak sesuai.');
            $this->redirect('surat');
        }

        $catatan   = trim($this->input('catatan', ''));
        $statusLama = $pengajuan['status'];
        $now        = date('Y-m-d H:i:s');

        $extraData = [
            'status'           => PengajuanSuratModel::STATUS_DITOLAK,
            'rw_approval_oleh' => (int) $user['id'],
            'rw_approval_at'   => $now,
            'rw_catatan'       => $catatan,
            'rw_status'        => 'ditolak',
            'updated_at'       => $now,
        ];
        $this->pengajuanModel->update((int) $id, $extraData);
        $this->historyModel->insert([
            'pengajuan_id'   => (int) $id,
            'status_lama'    => $statusLama,
            'status_baru'    => PengajuanSuratModel::STATUS_DITOLAK,
            'catatan'        => $catatan ?: 'Ditolak oleh RW',
            'dilakukan_oleh' => (int) $user['id'],
        ]);

        setFlash('warning', 'Pengajuan surat ditolak.');
        $this->redirect('surat/show/' . $id);
    }

    // ──────────────────────────────────────────
    // selesai - Tandai Selesai (Surat sudah diterima)
    // ──────────────────────────────────────────

    public function selesai(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('surat/show/' . $id);
        }

        $user     = authUser();
        $pengajuan = $this->pengajuanModel->find((int) $id);

        if (!$pengajuan || $pengajuan['status'] !== PengajuanSuratModel::STATUS_DISETUJUI) {
            setFlash('error', 'Status tidak sesuai untuk ditandai selesai.');
            $this->redirect('surat/show/' . $id);
        }

        $now = date('Y-m-d H:i:s');
        $this->pengajuanModel->update((int) $id, [
            'status'     => PengajuanSuratModel::STATUS_SELESAI,
            'updated_at' => $now,
        ]);
        $this->historyModel->insert([
            'pengajuan_id'   => (int) $id,
            'status_lama'    => PengajuanSuratModel::STATUS_DISETUJUI,
            'status_baru'    => PengajuanSuratModel::STATUS_SELESAI,
            'catatan'        => 'Surat telah diterima / selesai',
            'dilakukan_oleh' => (int) $user['id'],
        ]);

        setFlash('success', 'Surat ditandai selesai.');
        $this->redirect('surat/show/' . $id);
    }

    // ──────────────────────────────────────────
    // print - Cetak Surat (PDF-ready view)
    // ──────────────────────────────────────────

    public function print(string $id): void
    {
        $this->requireAuth();
        $user      = authUser();
        $pengajuan  = $this->pengajuanModel->findWithDetail((int) $id);

        if (!$pengajuan) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        // Warga hanya bisa cetak miliknya sendiri
        if ($user['role'] === 'warga' && (int) $pengajuan['dibuat_oleh'] !== (int) $user['id']) {
            http_response_code(403);
            $this->view('errors/403', ['title' => '403 - Akses Ditolak']);
            return;
        }

        // Hanya surat yang sudah disetujui atau selesai yang bisa dicetak
        if (!in_array($pengajuan['status'], [
            PengajuanSuratModel::STATUS_DISETUJUI,
            PengajuanSuratModel::STATUS_SELESAI,
        ], true)) {
            setFlash('warning', 'Surat hanya bisa dicetak setelah disetujui oleh RW.');
            $this->redirect('surat/show/' . $id);
        }

        // Render isi surat dari template
        $isiSurat = $this->renderTemplate($pengajuan);

        $this->view('surat/print', [
            'title'     => 'Cetak Surat - ' . APP_NAME,
            'pengajuan' => $pengajuan,
            'isiSurat'  => $isiSurat,
            'verifyUrl' => url('surat/verify/' . $pengajuan['kode_verifikasi']),
        ], null); // no layout for print
    }

    // ──────────────────────────────────────────
    // verify - Verifikasi keaslian via QR Code
    // ──────────────────────────────────────────

    public function verify(string $kode): void
    {
        $pengajuan = $this->pengajuanModel->findByKodeVerifikasi($kode);

        $this->view('surat/verify', [
            'title'     => 'Verifikasi Surat - ' . APP_NAME,
            'pengajuan' => $pengajuan ?: null,
            'kode'      => $kode,
        ], 'main');
    }

    // ──────────────────────────────────────────
    // history - Riwayat & Audit Log
    // ──────────────────────────────────────────

    public function history(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        $page       = (int) ($this->query('page', 1));
        $status     = $this->query('status', '');
        $keyword    = trim($this->query('keyword', ''));
        $pagination = $this->pengajuanModel->paginateWithFilter($page, $status, $keyword);

        $this->view('surat/history', [
            'title'      => 'Riwayat Surat - ' . APP_NAME,
            'pagination' => $pagination,
            'filterStatus' => $status,
            'keyword'    => $keyword,
        ]);
    }

    // ──────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────

    private function renderTemplate(array $pengajuan): string
    {
        $template = $pengajuan['jenis_template'] ?? '';
        if ($template === '') {
            return '';
        }

        $jk = $pengajuan['pemohon_jk'] === 'L' ? 'Laki-laki' : ($pengajuan['pemohon_jk'] === 'P' ? 'Perempuan' : '-');

        $placeholders = [
            '{nama}'         => $pengajuan['pemohon_nama']        ?? '-',
            '{nik}'          => $pengajuan['pemohon_nik']         ?? '-',
            '{tempat_lahir}' => $pengajuan['pemohon_tempat_lahir'] ?? '-',
            '{tgl_lahir}'    => $pengajuan['pemohon_tgl_lahir']
                                 ? formatDate($pengajuan['pemohon_tgl_lahir'])
                                 : '-',
            '{jenis_kelamin}' => $jk,
            '{agama}'        => $pengajuan['pemohon_agama']       ?? '-',
            '{pekerjaan}'    => $pengajuan['pemohon_pekerjaan']   ?? '-',
            '{alamat}'       => $pengajuan['pemohon_alamat']      ?? '-',
            '{rt}'           => $pengajuan['pemohon_rt']          ?? '-',
            '{keperluan}'    => $pengajuan['keperluan']            ?? '-',
            '{no_surat}'     => $pengajuan['no_surat']            ?? '-',
        ];

        return strtr($template, $placeholders);
    }
}
