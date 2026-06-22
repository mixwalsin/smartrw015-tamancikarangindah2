<?php

/**
 * AuthMiddleware – Validasi user sudah login
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Penggunaan di controller:
 *   AuthMiddleware::handle();
 */

declare(strict_types=1);

class AuthMiddleware
{
    /**
     * Pastikan user sudah login, redirect ke login jika belum.
     */
    public static function handle(): void
    {
        if (empty($_SESSION['user'])) {
            setFlash('error', 'Silakan login terlebih dahulu.');
            header('Location: ' . url('auth/login'));
            exit;
        }
    }

    /**
     * Validasi session aktif dan belum kadaluarsa.
     * Panggil di index.php / bootstrap jika perlu session timeout.
     */
    public static function validateSession(int $lifetimeSeconds = SESSION_LIFETIME): void
    {
        if (empty($_SESSION['user'])) {
            return;
        }

        $lastActivity = $_SESSION['last_activity'] ?? time();
        if ((time() - $lastActivity) > $lifetimeSeconds) {
            $_SESSION = [];
            session_destroy();
            header('Location: ' . url('auth/login?expired=1'));
            exit;
        }

        $_SESSION['last_activity'] = time();
    }
}
