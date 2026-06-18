<?php

/**
 * Helper Functions Global
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

// ──────────────────────────────────────────
// URL Helpers
// ──────────────────────────────────────────

/**
 * Generate URL relative to APP_URL
 */
function url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Generate URL for public assets
 */
function asset(string $path): string
{
    return url('public/assets/' . ltrim($path, '/'));
}

/**
 * Redirect helper
 */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

// ──────────────────────────────────────────
// Security Helpers
// ──────────────────────────────────────────

/**
 * Sanitize string output (XSS protection)
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output CSRF hidden input field
 */
function csrfField(): string
{
    return '<input type="hidden" name="_token" value="' . csrfToken() . '">';
}

/**
 * Verify CSRF token
 */
function verifyCsrf(): bool
{
    $token = $_POST['_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// ──────────────────────────────────────────
// Session / Flash Helpers
// ──────────────────────────────────────────

/**
 * Set a flash message
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and clear a flash message
 */
function getFlash(string $type): ?string
{
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user']);
}

/**
 * Get logged-in user data
 */
function authUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

// ──────────────────────────────────────────
// String / Format Helpers
// ──────────────────────────────────────────

/**
 * Format date to Indonesian locale
 */
function formatDate(string $date, string $format = 'd F Y'): string
{
    $months = [
        1  => 'Januari', 2  => 'Februari', 3  => 'Maret',
        4  => 'April',   5  => 'Mei',       6  => 'Juni',
        7  => 'Juli',    8  => 'Agustus',   9  => 'September',
        10 => 'Oktober', 11 => 'November',  12 => 'Desember',
    ];

    $timestamp = strtotime($date);
    $result    = date($format, $timestamp);

    foreach ($months as $num => $name) {
        $result = str_replace(date('F', mktime(0, 0, 0, $num, 1)), $name, $result);
    }

    return $result;
}

/**
 * Format number to Indonesian currency (Rupiah)
 */
function rupiah(int|float $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Truncate string
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Slug from string
 */
function slug(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ──────────────────────────────────────────
// File / Upload Helpers
// ──────────────────────────────────────────

/**
 * Upload a file to storage/uploads
 * Returns the file name or throws on error
 */
function uploadFile(array $file, string $subDir = ''): string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload gagal, kode error: ' . $file['error']);
    }

    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new RuntimeException('Ukuran file melebihi batas ' . (UPLOAD_MAX_SIZE / 1024 / 1024) . ' MB.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED_TYPES, true)) {
        throw new RuntimeException('Tipe file tidak diizinkan: ' . $ext);
    }

    $dir = UPLOAD_PATH . ($subDir ? '/' . trim($subDir, '/') : '');
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Gagal membuat direktori upload.');
    }

    $fileName = uniqid('', true) . '.' . $ext;
    $dest     = $dir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Gagal memindahkan file upload.');
    }

    return ($subDir ? trim($subDir, '/') . '/' : '') . $fileName;
}

// ──────────────────────────────────────────
// Pagination Helper
// ──────────────────────────────────────────

/**
 * Generate Bootstrap 5 pagination HTML
 */
function paginate(array $paginationData, string $urlBase): string
{
    $currentPage = $paginationData['current_page'];
    $lastPage    = $paginationData['last_page'];

    if ($lastPage <= 1) {
        return '';
    }

    $html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center">';

    // Previous
    $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
    $prevHref     = $currentPage <= 1 ? '#' : url($urlBase . '?page=' . ($currentPage - 1));
    $html .= "<li class=\"page-item{$prevDisabled}\"><a class=\"page-link\" href=\"{$prevHref}\">&laquo;</a></li>";

    // Pages
    for ($i = 1; $i <= $lastPage; $i++) {
        $active  = $i === $currentPage ? ' active' : '';
        $pageUrl = url($urlBase . '?page=' . $i);
        $html   .= "<li class=\"page-item{$active}\"><a class=\"page-link\" href=\"{$pageUrl}\">{$i}</a></li>";
    }

    // Next
    $nextDisabled = $currentPage >= $lastPage ? ' disabled' : '';
    $nextHref     = $currentPage >= $lastPage ? '#' : url($urlBase . '?page=' . ($currentPage + 1));
    $html .= "<li class=\"page-item{$nextDisabled}\"><a class=\"page-link\" href=\"{$nextHref}\">&raquo;</a></li>";

    $html .= '</ul></nav>';
    return $html;
}
