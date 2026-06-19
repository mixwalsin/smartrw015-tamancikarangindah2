<?php

/**
 * Core Router - Custom URL Routing
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

class Router
{
    private array $routes = [];

    public function get(string $path, string|array|callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string|array|callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function any(string $path, string|array|callable $handler): void
    {
        $this->addRoute('ANY', $path, $handler);
    }

    private function addRoute(string $method, string $path, string|array|callable $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $this->parseUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') {
                continue;
            }

            $params = $this->matchRoute($route['path'], $uri);
            if ($params !== null) {
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        $this->handleNotFound();
    }

    private function parseUri(): string
    {
        if (isset($_GET['url'])) {
            $uri = trim((string) $_GET['url'], '/');
        } else {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            $basePath   = str_replace($_SERVER['DOCUMENT_ROOT'] ?? '', '', BASE_PATH);
            $uri        = trim(parse_url($requestUri, PHP_URL_PATH) ?? '/', '/');
            $baseUri    = trim($basePath, '/');
            if ($baseUri !== '' && str_starts_with($uri, $baseUri)) {
                $uri = trim(substr($uri, strlen($baseUri)), '/');
            }
        }

        return $uri === '' ? '/' : '/' . $uri;
    }

    private function matchRoute(string $pattern, string $uri): ?array
    {
        $regex = preg_replace('/\/:([a-zA-Z_][a-zA-Z0-9_]*)/', '/(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        return array_filter($matches, static fn ($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
    }

    private function callHandler(string|array|callable $handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
        } elseif (is_array($handler)) {
            [$class, $method] = $handler;
        } else {
            $this->handleNotFound();
            return;
        }

        $controllerFile = APP_PATH . '/controllers/' . str_replace('\\', '/', $class) . '.php';
        if (!file_exists($controllerFile)) {
            $this->handleNotFound();
            return;
        }

        require_once $controllerFile;

        if (!class_exists($class)) {
            $this->handleNotFound();
            return;
        }

        $controller = new $class();
        if (!method_exists($controller, $method)) {
            $this->handleNotFound();
            return;
        }

        call_user_func_array([$controller, $method], $params);
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        $viewFile = APP_PATH . '/views/errors/404.php';
        if (file_exists($viewFile)) {
            require $viewFile;
            return;
        }

        echo '<h1>404 - Halaman tidak ditemukan</h1>';
    }
}
