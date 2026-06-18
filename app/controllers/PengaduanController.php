<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/PengaduanService.php';

class PengaduanController extends Controller
{
    private PengaduanService $service;

    public function __construct()
    {
        $this->service = new PengaduanService();
    }

    public function index(): void
    {
        $this->requireAuth();
        $filters = [
            'keyword' => trim((string) $this->query('keyword', '')),
            'status' => (string) $this->query('status', ''),
            'kategori_id' => (string) $this->query('kategori_id', ''),
            'prioritas' => (string) $this->query('prioritas', ''),
            'date_from' => (string) $this->query('date_from', ''),
            'date_to' => (string) $this->query('date_to', ''),
        ];
        $page = (int) $this->query('page', 1);

        $dashboard = $this->service->dashboard($filters, $page, authUser() ?? []);

        $this->view('pengaduan/index', [
            'title' => 'Sistem Pengaduan Warga - ' . APP_NAME,
            'filters' => $filters,
            'summary' => $dashboard['summary'],
            'pagination' => $dashboard['pagination'],
            'categories' => $dashboard['categories'],
            'statuses' => $this->service->statuses(),
            'priorities' => $this->service->priorities(),
            'notifications' => $dashboard['notifications'],
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('pengaduan/create', [
            'title' => 'Buat Pengaduan Baru - ' . APP_NAME,
            'categories' => $this->service->categories(),
            'priorities' => $this->service->priorities(),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan/create');
        }

        try {
            $id = $this->service->create($_POST, $_FILES, authUser() ?? []);
            setFlash('success', 'Pengaduan berhasil dikirim.');
            $this->redirect('pengaduan/show/' . $id);
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('pengaduan/create');
        }
    }

    public function show(string $id): void
    {
        $this->requireAuth();

        try {
            $pengaduan = $this->service->findDetail((int) $id, authUser() ?? []);
            if (!$pengaduan) {
                http_response_code(404);
                $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
                return;
            }

            $this->view('pengaduan/show', [
                'title' => 'Detail Pengaduan - ' . APP_NAME,
                'pengaduan' => $pengaduan,
                'statuses' => $this->service->statuses(),
                'priorities' => $this->service->priorities(),
            ]);
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('pengaduan');
        }
    }

    public function updateStatus(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'super_admin', 'rw', 'ketua_rw', 'sekretaris_rw', 'bendahara_rw', 'rt', 'ketua_rt', 'admin_rt');
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan/show/' . $id);
        }

        try {
            $this->service->updateStatus((int) $id, $_POST, authUser() ?? []);
            setFlash('success', 'Status pengaduan berhasil diperbarui.');
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('pengaduan/show/' . $id);
    }
}
