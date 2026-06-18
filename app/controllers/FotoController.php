<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/FileUploadService.php';
require_once APP_PATH . '/services/PengaduanService.php';

class FotoController extends Controller
{
    private PengaduanService $service;
    private FileUploadService $uploads;

    public function __construct()
    {
        $this->service = new PengaduanService();
        $this->uploads = new FileUploadService();
    }

    public function store(string $id): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('pengaduan/show/' . $id);
        }

        try {
            $this->service->addPhotos((int) $id, $_FILES, authUser() ?? []);
            setFlash('success', 'Foto tambahan berhasil diunggah.');
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('pengaduan/show/' . $id);
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
            $this->service->deletePhoto((int) $id, authUser() ?? []);
            setFlash('success', 'Foto berhasil dihapus.');
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
        }

        $this->redirect('pengaduan/show/' . $pengaduanId);
    }

    public function download(string $id): void
    {
        $this->requireAuth();

        try {
            $photo = $this->service->findPhoto((int) $id, authUser() ?? []);
            if (!$photo) {
                http_response_code(404);
                echo 'Foto tidak ditemukan.';
                return;
            }

            $path = $this->uploads->absolutePath((string) $photo['foto_path']);
            $realPath = realpath($path);
            $basePath = realpath(UPLOAD_PATH);
            if ($realPath === false || $basePath === false || !str_starts_with($realPath, $basePath) || !is_file($realPath)) {
                http_response_code(404);
                echo 'File tidak tersedia.';
                return;
            }

            header('Content-Type: ' . (mime_content_type($realPath) ?: 'application/octet-stream'));
            header('Content-Length: ' . (string) filesize($realPath));
            header('Content-Disposition: attachment; filename="' . basename($realPath) . '"');
            readfile($realPath);
            exit;
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('pengaduan');
        }
    }
}
