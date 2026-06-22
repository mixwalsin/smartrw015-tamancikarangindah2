<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/PengaduanService.php';

class PengaduanKomentarController extends Controller
{
    private PengaduanService $service;

    public function __construct()
    {
        $this->service = new PengaduanService();
    }

    public function store(string $id): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan/show/' . $id);
        }

        try {
            $this->service->addComment((int) $id, $_POST, $_FILES, authUser() ?? []);
            setFlash('success', 'Komentar berhasil ditambahkan.');
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('pengaduan/show/' . $id);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan');
        }

        try {
            $this->service->updateComment((int) $id, $_POST, authUser() ?? []);
            setFlash('success', 'Komentar berhasil diperbarui.');
            $this->redirect('pengaduan/show/' . (int) ($_POST['pengaduan_id'] ?? 0));
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('pengaduan');
        }
    }

    public function delete(string $id): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan');
        }

        $pengaduanId = (int) ($_POST['pengaduan_id'] ?? 0);
        try {
            $this->service->deleteComment((int) $id, authUser() ?? []);
            setFlash('success', 'Komentar berhasil dihapus.');
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('pengaduan/show/' . $pengaduanId);
    }
}
