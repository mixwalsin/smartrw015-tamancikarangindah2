<?php

/**
 * PendudukController
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/PendudukModel.php';

class PendudukController extends Controller
{
    private PendudukModel $model;

    public function __construct()
    {
        $this->model = new PendudukModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $page = max(1, (int) $this->query('page', 1));
        $keyword = trim((string) $this->query('keyword', ''));

        $this->view('penduduk/index', [
            'title'      => 'Data Penduduk - ' . APP_NAME,
            'pagination' => $this->model->paginateWithSearch($page, $keyword),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $this->view('penduduk/create', [
            'title' => 'Tambah Penduduk - ' . APP_NAME,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('penduduk/create');
        }

        $data = [
            'nik'           => trim((string) $this->input('nik', '')),
            'nama'          => trim((string) $this->input('nama', '')),
            'tempat_lahir'  => trim((string) $this->input('tempat_lahir', '')),
            'tanggal_lahir' => (string) $this->input('tanggal_lahir', ''),
            'jenis_kelamin' => (string) $this->input('jenis_kelamin', ''),
            'alamat'        => trim((string) $this->input('alamat', '')),
            'rt'            => str_pad((string) $this->input('rt', ''), 3, '0', STR_PAD_LEFT),
            'status_kawin'  => (string) $this->input('status_kawin', ''),
            'agama'         => (string) $this->input('agama', ''),
            'pekerjaan'     => trim((string) $this->input('pekerjaan', '')),
            'no_kk'         => trim((string) $this->input('no_kk', '')),
        ];

        if ($data['nik'] === '' || $data['nama'] === '' || $data['no_kk'] === '' || $data['rt'] === '' || $data['alamat'] === '') {
            setFlash('error', 'NIK, Nama, No. KK, RT, dan alamat wajib diisi.');
            $this->redirect('penduduk/create');
        }

        if ($this->model->findByNik($data['nik'])) {
            setFlash('error', 'NIK sudah terdaftar.');
            $this->redirect('penduduk/create');
        }

        try {
            $id = $this->model->insertFromForm($data);
            logActivity('create', 'warga', (int) $id, 'Menambah data penduduk baru');
            setFlash('success', 'Data penduduk berhasil ditambahkan.');
        } catch (Throwable $e) {
            setFlash('error', 'Gagal menyimpan data penduduk: ' . $e->getMessage());
        }

        $this->redirect('penduduk');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $penduduk = $this->model->findDetailed((int) $id);
        if (!$penduduk) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }

        $this->renderModuleShow([
            'title'     => 'Detail Penduduk',
            'icon'      => 'bi-people',
            'routeBase' => 'penduduk',
            'row'       => $penduduk,
            'columns'   => [
                ['key' => 'nik', 'label' => 'NIK'],
                ['key' => 'nama', 'label' => 'Nama'],
                ['key' => 'no_kk', 'label' => 'No. KK'],
                ['key' => 'rt', 'label' => 'RT'],
                ['key' => 'alamat', 'label' => 'Alamat'],
                ['key' => 'jenis_kelamin', 'label' => 'Jenis Kelamin'],
                ['key' => 'agama', 'label' => 'Agama'],
                ['key' => 'pekerjaan', 'label' => 'Pekerjaan'],
            ],
        ]);
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $penduduk = $this->model->findDetailed((int) $id);
        if (!$penduduk) {
            $this->redirect('penduduk');
        }

        $this->renderModuleForm([
            'title'      => 'Edit Penduduk',
            'icon'       => 'bi-pencil-square',
            'routeBase'  => 'penduduk',
            'actionUrl'  => url('penduduk/update/' . $id),
            'submitText' => 'Perbarui',
            'fields'     => [
                'nik' => ['label' => 'NIK', 'type' => 'text', 'value' => $penduduk['nik'], 'readonly' => true],
                'no_kk' => ['label' => 'No. KK', 'type' => 'text', 'value' => $penduduk['no_kk'], 'required' => true],
                'nama' => ['label' => 'Nama Lengkap', 'type' => 'text', 'value' => $penduduk['nama'], 'required' => true],
                'tempat_lahir' => ['label' => 'Tempat Lahir', 'type' => 'text', 'value' => $penduduk['tempat_lahir']],
                'tanggal_lahir' => ['label' => 'Tanggal Lahir', 'type' => 'date', 'value' => $penduduk['tanggal_lahir']],
                'jenis_kelamin' => ['label' => 'Jenis Kelamin', 'type' => 'select', 'value' => $penduduk['jenis_kelamin'], 'options' => ['L' => 'Laki-laki', 'P' => 'Perempuan']],
                'rt' => ['label' => 'RT', 'type' => 'select', 'value' => $penduduk['rt'], 'options' => [
                    '001' => 'RT 001', '002' => 'RT 002', '003' => 'RT 003', '004' => 'RT 004', '005' => 'RT 005', '006' => 'RT 006', '007' => 'RT 007',
                ]],
                'status_kawin' => ['label' => 'Status Kawin', 'type' => 'select', 'value' => $penduduk['status_kawin'], 'options' => [
                    'Belum Kawin' => 'Belum Kawin', 'Kawin' => 'Kawin', 'Cerai Hidup' => 'Cerai Hidup', 'Cerai Mati' => 'Cerai Mati',
                ]],
                'agama' => ['label' => 'Agama', 'type' => 'select', 'value' => $penduduk['agama'], 'options' => [
                    'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu',
                ]],
                'pekerjaan' => ['label' => 'Pekerjaan', 'type' => 'text', 'value' => $penduduk['pekerjaan']],
                'alamat' => ['label' => 'Alamat', 'type' => 'textarea', 'value' => $penduduk['alamat'], 'required' => true],
            ],
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('penduduk/edit/' . $id);
        }

        $data = [
            'nik'           => trim((string) $this->input('nik', '')),
            'nama'          => trim((string) $this->input('nama', '')),
            'tempat_lahir'  => trim((string) $this->input('tempat_lahir', '')),
            'tanggal_lahir' => (string) $this->input('tanggal_lahir', ''),
            'jenis_kelamin' => (string) $this->input('jenis_kelamin', ''),
            'alamat'        => trim((string) $this->input('alamat', '')),
            'rt'            => str_pad((string) $this->input('rt', ''), 3, '0', STR_PAD_LEFT),
            'status_kawin'  => (string) $this->input('status_kawin', ''),
            'agama'         => (string) $this->input('agama', ''),
            'pekerjaan'     => trim((string) $this->input('pekerjaan', '')),
            'no_kk'         => trim((string) $this->input('no_kk', '')),
        ];

        try {
            $this->model->updateFromForm((int) $id, $data);
            logActivity('update', 'warga', (int) $id, 'Memperbarui data penduduk');
            setFlash('success', 'Data penduduk berhasil diperbarui.');
        } catch (Throwable $e) {
            setFlash('error', 'Gagal memperbarui data penduduk: ' . $e->getMessage());
        }

        $this->redirect('penduduk');
    }

    public function delete(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('penduduk');
        }

        $this->model->delete((int) $id);
        logActivity('delete', 'warga', (int) $id, 'Menghapus data penduduk');
        setFlash('success', 'Data penduduk berhasil dihapus.');
        $this->redirect('penduduk');
    }
}
