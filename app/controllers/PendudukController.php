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
        $page = (int) ($this->query('page', 1));
        $pagination = $this->model->paginate($page);

        $this->view('penduduk/index', [
            'title'      => 'Data Penduduk - ' . APP_NAME,
            'pagination' => $pagination,
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
            'nik'           => trim($this->input('nik', '')),
            'nama'          => trim($this->input('nama', '')),
            'tempat_lahir'  => trim($this->input('tempat_lahir', '')),
            'tanggal_lahir' => $this->input('tanggal_lahir', ''),
            'jenis_kelamin' => $this->input('jenis_kelamin', ''),
            'alamat'        => trim($this->input('alamat', '')),
            'rt'            => $this->input('rt', ''),
            'rw'            => '015',
            'status_kawin'  => $this->input('status_kawin', ''),
            'agama'         => $this->input('agama', ''),
            'pekerjaan'     => trim($this->input('pekerjaan', '')),
            'no_kk'         => trim($this->input('no_kk', '')),
        ];

        if ($data['nik'] === '' || $data['nama'] === '') {
            setFlash('error', 'NIK dan Nama wajib diisi.');
            $this->redirect('penduduk/create');
        }

        if ($this->model->findByNik($data['nik'])) {
            setFlash('error', 'NIK sudah terdaftar.');
            $this->redirect('penduduk/create');
        }

        $this->model->insert($data);
        setFlash('success', 'Data penduduk berhasil ditambahkan.');
        $this->redirect('penduduk');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $penduduk = $this->model->find((int) $id);
        if (!$penduduk) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }
        $this->view('penduduk/show', [
            'title'    => 'Detail Penduduk - ' . APP_NAME,
            'penduduk' => $penduduk,
        ]);
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $penduduk = $this->model->find((int) $id);
        if (!$penduduk) {
            $this->redirect('penduduk');
        }
        $this->view('penduduk/edit', [
            'title'    => 'Edit Penduduk - ' . APP_NAME,
            'penduduk' => $penduduk,
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
            'nama'          => trim($this->input('nama', '')),
            'tempat_lahir'  => trim($this->input('tempat_lahir', '')),
            'tanggal_lahir' => $this->input('tanggal_lahir', ''),
            'jenis_kelamin' => $this->input('jenis_kelamin', ''),
            'alamat'        => trim($this->input('alamat', '')),
            'rt'            => $this->input('rt', ''),
            'status_kawin'  => $this->input('status_kawin', ''),
            'agama'         => $this->input('agama', ''),
            'pekerjaan'     => trim($this->input('pekerjaan', '')),
            'no_kk'         => trim($this->input('no_kk', '')),
        ];

        $this->model->update((int) $id, $data);
        setFlash('success', 'Data penduduk berhasil diperbarui.');
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
        setFlash('success', 'Data penduduk berhasil dihapus.');
        $this->redirect('penduduk');
    }
}
