<?php

/**
 * KartuKeluargaController
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/KartuKeluargaModel.php';
require_once APP_PATH . '/models/AnggotaKeluargaModel.php';
require_once APP_PATH . '/models/RiwayatKKModel.php';

class KartuKeluargaController extends Controller
{
    private KartuKeluargaModel $kartuKeluargaModel;
    private AnggotaKeluargaModel $anggotaKeluargaModel;
    private RiwayatKKModel $riwayatKKModel;

    public function __construct()
    {
        $this->kartuKeluargaModel = new KartuKeluargaModel();
        $this->anggotaKeluargaModel = new AnggotaKeluargaModel();
        $this->riwayatKKModel = new RiwayatKKModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $page = (int) $this->query('page', 1);
        $pagination = $this->kartuKeluargaModel->paginateWithStats($page);

        $this->view('kartu_keluarga/index', [
            'title'      => 'Data Kartu Keluarga - ' . APP_NAME,
            'pagination' => $pagination,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $this->view('kartu_keluarga/create', [
            'title' => 'Tambah Kartu Keluarga - ' . APP_NAME,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('kartu-keluarga/create');
        }

        $data = $this->buildKkPayload();
        if ($data['nomor_kk'] === '' || $data['kepala_keluarga'] === '' || $data['alamat'] === '') {
            setFlash('error', 'Nomor KK, Kepala Keluarga, dan Alamat wajib diisi.');
            $this->redirect('kartu-keluarga/create');
        }

        if ($this->kartuKeluargaModel->findByNomorKk($data['nomor_kk'])) {
            setFlash('error', 'Nomor KK sudah terdaftar.');
            $this->redirect('kartu-keluarga/create');
        }

        $kkId = $this->kartuKeluargaModel->insert($data);
        if ($kkId === false) {
            setFlash('error', 'Gagal menambahkan data KK.');
            $this->redirect('kartu-keluarga/create');
        }

        $this->riwayatKKModel->log((int) $kkId, 'Tambah KK', 'Data KK baru ditambahkan.', null, json_encode($data));
        setFlash('success', 'Data KK berhasil ditambahkan.');
        $this->redirect('kartu-keluarga/show/' . $kkId);
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $kk = $this->kartuKeluargaModel->find((int) $id);
        if (!$kk) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        $this->kartuKeluargaModel->syncJumlahAnggota((int) $id);
        $kk = $this->kartuKeluargaModel->find((int) $id);

        $this->view('kartu_keluarga/show', [
            'title'         => 'Detail Kartu Keluarga - ' . APP_NAME,
            'kk'            => $kk,
            'anggota'       => $this->anggotaKeluargaModel->getByKartuKeluarga((int) $id),
            'riwayat'       => $this->riwayatKKModel->getByKartuKeluarga((int) $id),
            'wargaOptions'  => $this->getWargaOptions(),
        ]);
    }

    public function storeAnggota(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('kartu-keluarga/show/' . $id);
        }

        $kkId = (int) $id;
        $wargaId = (int) $this->input('warga_id', 0);
        $hubungan = trim((string) $this->input('hubungan', 'Lainnya'));

        if ($wargaId <= 0) {
            setFlash('error', 'Warga wajib dipilih.');
            $this->redirect('kartu-keluarga/show/' . $id);
        }

        if ($this->anggotaKeluargaModel->existsWargaInKartuKeluarga($kkId, $wargaId)) {
            setFlash('error', 'Warga sudah terdaftar sebagai anggota KK ini.');
            $this->redirect('kartu-keluarga/show/' . $id);
        }

        $this->anggotaKeluargaModel->insert([
            'kartu_keluarga_id' => $kkId,
            'warga_id'          => $wargaId,
            'hubungan'          => $hubungan,
        ]);
        $this->kartuKeluargaModel->syncJumlahAnggota($kkId);
        $this->riwayatKKModel->log($kkId, 'Tambah Anggota', 'Tambah anggota keluarga.', null, json_encode([
            'warga_id' => $wargaId,
            'hubungan' => $hubungan,
        ]));

        setFlash('success', 'Anggota keluarga berhasil ditambahkan.');
        $this->redirect('kartu-keluarga/show/' . $id);
    }

    public function pindah(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $kk = $this->kartuKeluargaModel->find((int) $id);
        if (!$kk) {
            $this->redirect('kartu-keluarga');
        }

        $this->view('kartu_keluarga/pindah', [
            'title' => 'Pindah KK - ' . APP_NAME,
            'kk'    => $kk,
        ]);
    }

    public function prosesPindah(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('kartu-keluarga/pindah/' . $id);
        }

        $kkId = (int) $id;
        $kk = $this->kartuKeluargaModel->find($kkId);
        if (!$kk) {
            setFlash('error', 'Data KK tidak ditemukan.');
            $this->redirect('kartu-keluarga');
        }

        $data = [
            'alamat' => trim((string) $this->input('alamat', '')),
            'rt'     => formatRtRw((string) $this->input('rt', '')),
            'rw'     => formatRtRw((string) $this->input('rw', '')),
        ];

        $this->kartuKeluargaModel->update($kkId, $data);
        $this->riwayatKKModel->log(
            $kkId,
            'Pindah KK',
            'Perubahan lokasi RT/RW/alamat.',
            json_encode(['alamat' => $kk['alamat'], 'rt' => $kk['rt'], 'rw' => $kk['rw']]),
            json_encode($data)
        );

        setFlash('success', 'Data lokasi KK berhasil diperbarui.');
        $this->redirect('kartu-keluarga/show/' . $id);
    }

    public function print(string $id): void
    {
        $this->requireAuth();
        $kk = $this->kartuKeluargaModel->find((int) $id);
        if (!$kk) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        $this->view('kartu_keluarga/print', [
            'title'   => 'Cetak KK Internal',
            'kk'      => $kk,
            'anggota' => $this->anggotaKeluargaModel->getByKartuKeluarga((int) $id),
        ], null);
    }

    public function apiIndex(): void
    {
        $this->requireAuth();
        $this->json($this->kartuKeluargaModel->all('id', 'DESC'));
    }

    public function apiShow(string $id): void
    {
        $this->requireAuth();
        $kk = $this->kartuKeluargaModel->find((int) $id);
        if (!$kk) {
            $this->json(['message' => 'Data KK tidak ditemukan.'], 404);
        }
        $this->json([
            'kk'      => $kk,
            'anggota' => $this->anggotaKeluargaModel->getByKartuKeluarga((int) $id),
            'riwayat' => $this->riwayatKKModel->getByKartuKeluarga((int) $id),
        ]);
    }

    public function apiStore(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        $data = $this->buildKkPayload();
        if ($data['nomor_kk'] === '' || $data['kepala_keluarga'] === '' || $data['alamat'] === '') {
            $this->json(['message' => 'Nomor KK, Kepala Keluarga, dan Alamat wajib diisi.'], 422);
        }
        $kkId = $this->kartuKeluargaModel->insert($data);
        if ($kkId === false) {
            $this->json(['message' => 'Gagal menyimpan data KK.'], 500);
        }
        $this->riwayatKKModel->log((int) $kkId, 'Tambah KK', 'Data KK baru ditambahkan via API.');
        $this->json(['message' => 'Berhasil', 'id' => (int) $kkId], 201);
    }

    public function apiStoreAnggota(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        $kkId = (int) $id;
        $wargaId = (int) $this->input('warga_id', 0);
        $hubungan = trim((string) $this->input('hubungan', 'Lainnya'));

        if ($wargaId <= 0) {
            $this->json(['message' => 'warga_id wajib diisi.'], 422);
        }
        if ($this->anggotaKeluargaModel->existsWargaInKartuKeluarga($kkId, $wargaId)) {
            $this->json(['message' => 'Warga sudah ada di KK ini.'], 409);
        }

        $this->anggotaKeluargaModel->insert([
            'kartu_keluarga_id' => $kkId,
            'warga_id'          => $wargaId,
            'hubungan'          => $hubungan,
        ]);
        $this->kartuKeluargaModel->syncJumlahAnggota($kkId);
        $this->riwayatKKModel->log($kkId, 'Tambah Anggota', 'Tambah anggota via API.');

        $this->json(['message' => 'Anggota berhasil ditambahkan.'], 201);
    }

    public function apiPindah(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        $kkId = (int) $id;
        $kk = $this->kartuKeluargaModel->find($kkId);
        if (!$kk) {
            $this->json(['message' => 'Data KK tidak ditemukan.'], 404);
        }

        $data = [
            'alamat' => trim((string) $this->input('alamat', '')),
            'rt'     => formatRtRw((string) $this->input('rt', '')),
            'rw'     => formatRtRw((string) $this->input('rw', '')),
        ];
        $this->kartuKeluargaModel->update($kkId, $data);
        $this->riwayatKKModel->log($kkId, 'Pindah KK', 'Perubahan lokasi via API.', json_encode($kk), json_encode($data));

        $this->json(['message' => 'Data lokasi KK diperbarui.']);
    }

    public function apiRiwayat(string $id): void
    {
        $this->requireAuth();
        $this->json($this->riwayatKKModel->getByKartuKeluarga((int) $id));
    }

    private function buildKkPayload(): array
    {
        return [
            'nomor_kk'        => preg_replace('/[^0-9]/', '', (string) $this->input('nomor_kk', '')) ?? '',
            'kepala_keluarga' => trim((string) $this->input('kepala_keluarga', '')),
            'alamat'          => trim((string) $this->input('alamat', '')),
            'rt'              => formatRtRw((string) $this->input('rt', '')),
            'rw'              => formatRtRw((string) $this->input('rw', '')),
            'jumlah_anggota'  => (int) $this->input('jumlah_anggota', 0),
        ];
    }

    private function getWargaOptions(): array
    {
        try {
            return $this->kartuKeluargaModel->query(
                "SELECT id, nik, nama FROM warga ORDER BY nama ASC LIMIT 200"
            );
        } catch (Throwable) {
            return [];
        }
    }
}
