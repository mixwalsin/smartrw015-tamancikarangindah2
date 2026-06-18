<?php

/**
 * Konfigurasi Aplikasi Smart RW015
 */

declare(strict_types=1);

// Environment: 'development' | 'production'
define('APP_ENV', 'development');

define('APP_NAME', 'Smart RW015 Taman Cikarang Indah 2');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/smartrw015-tamancikarangindah2');
define('APP_TIMEZONE', 'Asia/Jakarta');
define('APP_LOCALE', 'id_ID');

// Debug mode (nonaktifkan di production)
define('APP_DEBUG', APP_ENV === 'development');

// Session
define('SESSION_NAME', 'smartrw015_session');
define('SESSION_LIFETIME', 7200); // 2 jam

// Upload
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5 MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Pagination
define('PAGINATION_LIMIT', 10);

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => false, // set true jika HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
