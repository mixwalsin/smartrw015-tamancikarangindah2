<?php

declare(strict_types=1);

if (!defined('PENGADUAN_ALLOWED_PHOTO_TYPES')) {
    define('PENGADUAN_ALLOWED_PHOTO_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
}

if (!defined('PENGADUAN_MAX_PHOTOS')) {
    define('PENGADUAN_MAX_PHOTOS', 5);
}

if (!defined('PENGADUAN_DEFAULT_SLA_DAYS')) {
    define('PENGADUAN_DEFAULT_SLA_DAYS', 7);
}

if (!defined('PENGADUAN_STATUSES')) {
    define('PENGADUAN_STATUSES', [
        'diterima' => 'Diterima',
        'diproses_rt' => 'Diproses RT',
        'diproses_rw' => 'Diproses RW',
        'dalam_perbaikan' => 'Dalam Perbaikan',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
    ]);
}

if (!defined('PENGADUAN_PRIORITAS')) {
    define('PENGADUAN_PRIORITAS', [
        'rendah' => 'Rendah',
        'sedang' => 'Sedang',
        'tinggi' => 'Tinggi',
        'darurat' => 'Darurat',
    ]);
}

if (!defined('PENGADUAN_SLA_BY_CATEGORY')) {
    define('PENGADUAN_SLA_BY_CATEGORY', [
        'keamanan' => 1,
        'kebersihan' => 3,
        'infrastruktur' => 7,
        'lingkungan' => 5,
        'layanan-rw' => 2,
    ]);
}

if (!defined('PENGADUAN_NOTIFICATIONS')) {
    define('PENGADUAN_NOTIFICATIONS', [
        'email' => true,
        'sms' => false,
        'in_app' => true,
        'daily_digest' => true,
        'weekly_summary' => true,
        'escalation_hours' => 48,
    ]);
}
