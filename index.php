<?php

/**
 * Smart RW015 Taman Cikarang Indah 2
 * Front Controller - Entry Point
 *
 * PHP 8.2 | MySQL | Bootstrap 5 | XAMPP Compatible
 */

declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Autoload
spl_autoload_register(function (string $class): void {
    $paths = [
        APP_PATH . '/core/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
        APP_PATH . '/helpers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load config
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/database.php';

// Load helpers
require_once APP_PATH . '/helpers/functions.php';

// Dispatch
$router = new Router();
require_once BASE_PATH . '/routes/web.php';
$router->dispatch();
