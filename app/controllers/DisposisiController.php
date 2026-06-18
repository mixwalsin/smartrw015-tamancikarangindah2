<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/DisposisiService.php';

class DisposisiController extends Controller
{
    private DisposisiService $service;

    public function __construct()
    {
        $this->service = new DisposisiService();
    }

    public function storeRt(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'super_admin', 'rt', 'ketua_rt', 'admin_rt');
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan/show/' . $id);
        }

        try {
            $this->service->submitRtDisposition((int) $id, $_POST, authUser() ?? []);
            setFlash('success', 'Disposisi RT berhasil disimpan.');
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('pengaduan/show/' . $id);
    }

    public function storeRw(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'super_admin', 'rw', 'ketua_rw', 'sekretaris_rw', 'bendahara_rw');
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan/show/' . $id);
        }

        try {
            $this->service->submitRwDisposition((int) $id, $_POST, authUser() ?? []);
            setFlash('success', 'Disposisi RW berhasil disimpan.');
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('pengaduan/show/' . $id);
    }
}
