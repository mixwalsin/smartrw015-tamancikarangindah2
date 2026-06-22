<?php

declare(strict_types=1);

class FileUploadService
{
    public function uploadComplaintPhotos(array $files): array
    {
        $normalized = $this->normalizeFiles($files['foto'] ?? null);
        if ($normalized === []) {
            return [];
        }

        if (count($normalized) > PENGADUAN_MAX_PHOTOS) {
            throw new RuntimeException('Maksimal ' . PENGADUAN_MAX_PHOTOS . ' foto per pengaduan.');
        }

        $paths = [];
        foreach ($normalized as $file) {
            $ext = strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, PENGADUAN_ALLOWED_PHOTO_TYPES, true)) {
                throw new RuntimeException('Format foto harus JPG, PNG, atau GIF.');
            }
            $paths[] = uploadFile($file, 'pengaduan');
        }

        return $paths;
    }

    public function uploadCommentAttachment(?array $file): ?string
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return uploadFile($file, 'pengaduan/komentar');
    }

    public function absolutePath(string $relativePath): string
    {
        return UPLOAD_PATH . '/' . ltrim($relativePath, '/');
    }

    private function normalizeFiles(?array $files): array
    {
        if ($files === null || !isset($files['name'])) {
            return [];
        }

        if (!is_array($files['name'])) {
            return ($files['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE ? [] : [$files];
        }

        $normalized = [];
        foreach ($files['name'] as $index => $name) {
            if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $normalized[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $normalized;
    }
}
