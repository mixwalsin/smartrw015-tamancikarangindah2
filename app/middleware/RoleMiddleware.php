<?php

/**
 * RoleMiddleware – Validasi user memiliki role tertentu
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Penggunaan di controller:
 *   RoleMiddleware::handle(['super_admin', 'ketua_rw']);
 */

declare(strict_types=1);

class RoleMiddleware
{
    /**
     * Pastikan user memiliki salah satu role yang diizinkan.
     *
     * @param  string[] $allowedRoles  Daftar slug role yang boleh akses
     */
    public static function handle(array $allowedRoles): void
    {
        AuthMiddleware::handle();

        $userRole = $_SESSION['user']['role'] ?? '';
        if (!in_array($userRole, $allowedRoles, true)) {
            http_response_code(403);
            $viewFile = APP_PATH . '/views/errors/403.php';
            $message  = 'Anda tidak memiliki role yang diperlukan untuk mengakses halaman ini.';
            if (file_exists($viewFile)) {
                require $viewFile;
            } else {
                echo '<h1>403 – Akses Ditolak</h1><p>' . htmlspecialchars($message) . '</p>';
            }
            exit;
        }
    }
}
