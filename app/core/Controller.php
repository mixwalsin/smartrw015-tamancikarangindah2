<?php

/**
 * Base Controller
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
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
            require $viewFile;
            return;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }

    protected function redirect(string $url): never
    {
        if (!str_starts_with($url, 'http')) {
            $url = APP_URL . '/' . ltrim($url, '/');
        }
        header('Location: ' . $url);
        exit;
    }

    protected function json(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('auth/login');
        }
    }

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

    protected function requirePermission(string $permission): void
    {
        $this->requireAuth();
        if (!authCan($permission)) {
            http_response_code(403);
            $this->view('errors/403', ['message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.']);
            exit;
        }
    }

    protected function renderModuleIndex(array $data): void
    {
        $this->view('shared/module_index', $data);
    }

    protected function renderModuleForm(array $data): void
    {
        $this->view('shared/module_form', $data);
    }

    protected function renderModuleShow(array $data): void
    {
        $this->view('shared/module_show', $data);
    }
}
