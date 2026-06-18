<?php

/**
 * Base Controller
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

abstract class Controller
{
    /**
     * Render a view with optional layout
     *
     * @param string $view   Path relative to app/views/ (e.g. 'home/index')
     * @param array  $data   Variables to extract into the view
     * @param string|null $layout  Layout file relative to app/views/layouts/ (null = no layout)
     */
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        // Make data available as variables
        extract($data, EXTR_SKIP);

        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            die('View tidak ditemukan: ' . htmlspecialchars($view));
        }

        if ($layout === null) {
            require $viewFile;
            return;
        }

        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            // Fallback to no layout
            require $viewFile;
            return;
        }

        // Buffer content view
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }

    /**
     * Redirect to a URL
     */
    protected function redirect(string $url): never
    {
        if (!str_starts_with($url, 'http')) {
            $url = APP_URL . '/' . ltrim($url, '/');
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Check if request is POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get POST input with optional default
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET param with optional default
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Require authentication; redirect to login if not logged in
     */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('auth/login');
        }
    }

    /**
     * Require a specific role
     */
    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        $userRole = $_SESSION['user']['role'] ?? '';
        if (!in_array($userRole, $roles, true)) {
            http_response_code(403);
            $this->view('errors/403', ['message' => 'Anda tidak memiliki akses ke halaman ini.']);
            exit;
        }
    }

    /**
     * Require a specific permission
     */
    protected function requirePermission(string $permission): void
    {
        $this->requireAuth();
        if (!$this->can($permission)) {
            http_response_code(403);
            $this->view('errors/403', [
                'message' => "Anda tidak memiliki izin: {$permission}.",
            ]);
            exit;
        }
    }

    /**
     * Require any of the given permissions
     */
    protected function requireAnyPermission(array $permissions): void
    {
        $this->requireAuth();
        foreach ($permissions as $perm) {
            if ($this->can($perm)) {
                return;
            }
        }
        http_response_code(403);
        $this->view('errors/403', ['message' => 'Anda tidak memiliki izin yang diperlukan.']);
        exit;
    }

    /**
     * Check if current user has a specific permission
     */
    protected function can(string $permission): bool
    {
        static $rbac = null;
        if ($rbac === null) {
            require_once APP_PATH . '/core/RbacService.php';
            $rbac = new RbacService();
        }
        return $rbac->can($permission);
    }

    /**
     * Check if current user has a specific role
     */
    protected function hasRole(string $role): bool
    {
        return ($this->currentUser()['role'] ?? '') === $role;
    }

    /**
     * Check if current user has any of the given roles
     */
    protected function hasAnyRole(array $roles): bool
    {
        return in_array($this->currentUser()['role'] ?? '', $roles, true);
    }

    /**
     * Get current logged-in user data
     */
    protected function currentUser(): array
    {
        return $_SESSION['user'] ?? [];
    }

    /**
     * Log an activity to audit trail
     */
    protected function logActivity(
        string $aksi,
        string $modul,
        ?int   $dataId     = null,
        string $keterangan = ''
    ): void {
        require_once APP_PATH . '/models/AuditLogModel.php';
        $userId = (int) ($_SESSION['user']['id'] ?? 0);
        (new AuditLogModel())->log($userId ?: null, $aksi, $modul, $dataId, $keterangan);
    }
}
