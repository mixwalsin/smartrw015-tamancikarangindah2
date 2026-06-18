<?php

/**
 * HomeController
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'title'    => 'Beranda - ' . APP_NAME,
            'pageTitle' => 'Selamat Datang di ' . APP_NAME,
        ]);
    }
}
