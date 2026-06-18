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
            $this->view('errors/403', ['message' => 'Akses ditolak.']);
            exit;
        }
    }
}
