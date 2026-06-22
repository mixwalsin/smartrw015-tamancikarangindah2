<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/KasService.php';

class KasBalanceController extends Controller
{
    private KasService $service;

    public function __construct()
    {
        $this->service = new KasService();
    }

    public function index(): void
    {
        $this->requireAuth();

        $this->view('keuangan/balance', [
            'title' => 'Saldo Kas RW/RT - ' . APP_NAME,
            'balances' => $this->service->getBalances(),
        ]);
    }
}
