<?php

/**
 * Helper Functions Global
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

function url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('public/assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="_token" value="' . csrfToken() . '">';
}

function verifyCsrf(): bool
{
    $token = $_POST['_token'] ?? '';
    return $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

function getFlash(string $type): ?string
{
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']);
}

function authUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function authCan(string $permission): bool
{
    $permissions = $_SESSION['user']['permissions'] ?? [];
    return in_array($permission, $permissions, true) || in_array(authUser()['role'] ?? '', ['admin'], true);
}

function formatDate(string $date, string $format = 'd F Y'): string
{
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }

    $result = date($format, $timestamp);
    foreach ($months as $num => $name) {
        $result = str_replace(date('F', mktime(0, 0, 0, $num, 1)), $name, $result);
    }

    return $result;
}

function rupiah(int|float $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

function slug(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim((string) $text, '-');
}

function uploadFile(array $file, string $subDir = ''): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload gagal, kode error: ' . ($file['error'] ?? 'unknown'));
    }

    if (($file['size'] ?? 0) > UPLOAD_MAX_SIZE) {
        throw new RuntimeException('Ukuran file melebihi batas ' . (UPLOAD_MAX_SIZE / 1024 / 1024) . ' MB.');
    }

    $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED_TYPES, true)) {
        throw new RuntimeException('Tipe file tidak diizinkan: ' . $ext);
    }

    $dir = UPLOAD_PATH . ($subDir ? '/' . trim($subDir, '/') : '');
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Gagal membuat direktori upload.');
    }

    $fileName = uniqid('', true) . '.' . $ext;
    $dest     = $dir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Gagal memindahkan file upload.');
    }

    return ($subDir ? trim($subDir, '/') . '/' : '') . $fileName;
}

function paginate(array $paginationData, string $urlBase): string
{
    $currentPage = $paginationData['current_page'];
    $lastPage    = $paginationData['last_page'];
    $keyword     = $paginationData['keyword'] ?? '';

    if ($lastPage <= 1) {
        return '';
    }

    $querySuffix = $keyword !== '' ? '&keyword=' . urlencode($keyword) : '';
    $html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center mb-0">';

    $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
    $prevHref     = $currentPage <= 1 ? '#' : url($urlBase . '?page=' . ($currentPage - 1) . $querySuffix);
    $html .= "<li class=\"page-item{$prevDisabled}\"><a class=\"page-link\" href=\"{$prevHref}\">&laquo;</a></li>";

    for ($i = 1; $i <= $lastPage; $i++) {
        $active  = $i === $currentPage ? ' active' : '';
        $pageUrl = url($urlBase . '?page=' . $i . $querySuffix);
        $html   .= "<li class=\"page-item{$active}\"><a class=\"page-link\" href=\"{$pageUrl}\">{$i}</a></li>";
    }

    $nextDisabled = $currentPage >= $lastPage ? ' disabled' : '';
    $nextHref     = $currentPage >= $lastPage ? '#' : url($urlBase . '?page=' . ($currentPage + 1) . $querySuffix);
    $html .= "<li class=\"page-item{$nextDisabled}\"><a class=\"page-link\" href=\"{$nextHref}\">&raquo;</a></li>";

    $html .= '</ul></nav>';
    return $html;
}

function notificationCount(): int
{
    if (!isLoggedIn()) {
        return 0;
    }

    try {
        $model = new NotificationModel();
        return $model->unreadCountForUser((int) (authUser()['id'] ?? 0));
    } catch (Throwable) {
        return 0;
    }
}

function notificationItems(): array
{
    if (!isLoggedIn()) {
        return [];
    }

    try {
        $model = new NotificationModel();
        return $model->listForUser((int) (authUser()['id'] ?? 0));
    } catch (Throwable) {
        return [];
    }
}

function logActivity(string $aksi, string $modul, ?int $dataId = null, ?string $keterangan = null): void
{
    try {
        $model = new AuditLogModel();
        $model->insert([
            'user_id'    => authUser()['id'] ?? null,
            'aksi'       => $aksi,
            'modul'      => $modul,
            'data_id'    => $dataId,
            'keterangan' => $keterangan,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => truncate((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'CLI'), 255, ''),
        ]);
    } catch (Throwable) {
    }
}

function whatsappConfig(): array
{
    return $GLOBALS['whatsapp_config'] ?? [];
}
