<?php

/**
 * PermissionMiddleware – Validasi user memiliki permission tertentu
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Penggunaan di controller:
 *   PermissionMiddleware::handle('warga.read');
 *   PermissionMiddleware::handleAny(['warga.read', 'warga.create']);
 */

declare(strict_types=1);

class PermissionMiddleware
{
    /**
     * Pastikan user memiliki permission tertentu.
     */
    public static function handle(string $permission): void
    {
        AuthMiddleware::handle();

        if (!can($permission)) {
            http_response_code(403);
            $viewFile = APP_PATH . '/views/errors/403.php';
            $message  = "Anda tidak memiliki izin: {$permission}.";
            if (file_exists($viewFile)) {
                require $viewFile;
            } else {
                echo '<h1>403 – Akses Ditolak</h1><p>' . htmlspecialchars($message) . '</p>';
            }
            exit;
        }
    }

    /**
     * Pastikan user memiliki salah satu dari beberapa permission.
     */
    public static function handleAny(array $permissions): void
    {
        AuthMiddleware::handle();

        foreach ($permissions as $perm) {
            if (can($perm)) {
                return;
            }
        }

        http_response_code(403);
        $viewFile = APP_PATH . '/views/errors/403.php';
        $message  = 'Anda tidak memiliki izin yang diperlukan.';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo '<h1>403 – Akses Ditolak</h1><p>' . htmlspecialchars($message) . '</p>';
        }
        exit;
    }
}
