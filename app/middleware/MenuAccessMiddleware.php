<?php

/**
 * MenuAccessMiddleware – Kontrol akses menu berdasarkan permission
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Penggunaan:
 *   MenuAccessMiddleware::check('warga.read')
 *   MenuAccessMiddleware::checkAny(['keuangan.read', 'keuangan.create'])
 */

declare(strict_types=1);

class MenuAccessMiddleware
{
    /**
     * Cek apakah menu boleh ditampilkan berdasarkan satu permission.
     * Mengembalikan true/false (tidak redirect) – untuk dipakai di view.
     */
    public static function check(string $permission): bool
    {
        return isLoggedIn() && can($permission);
    }

    /**
     * Cek apakah menu boleh ditampilkan berdasarkan salah satu permission.
     */
    public static function checkAny(array $permissions): bool
    {
        if (!isLoggedIn()) {
            return false;
        }
        foreach ($permissions as $perm) {
            if (can($perm)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Menampilkan atau menyembunyikan HTML menu item berdasarkan permission.
     * Gunakan ob_start/ob_get_clean di blade-style view jika perlu.
     *
     * Contoh:
     *   <?php if (MenuAccessMiddleware::check('warga.read')): ?>
     *     <a href="...">Penduduk</a>
     *   <?php endif; ?>
     */
}
