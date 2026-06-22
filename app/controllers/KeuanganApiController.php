<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/KasService.php';
require_once APP_PATH . '/services/KasReportService.php';

class KeuanganApiController extends Controller
{
    private KasService $kasService;
    private KasReportService $reportService;

    public function __construct()
    {
        $this->kasService = new KasService();
        $this->reportService = new KasReportService();
    }

    public function transactions(): void
    {
        $this->requireAuth();
        $data = $this->kasService->listTransactions([
            'kas_type' => $this->query('kas_type', ''),
            'transaction_type' => $this->query('transaction_type', ''),
            'status' => $this->query('status', ''),
            'date_from' => $this->query('date_from', ''),
            'date_to' => $this->query('date_to', ''),
            'search' => $this->query('search', ''),
            'rt_id' => $this->query('rt_id', ''),
            'category_id' => $this->query('category_id', ''),
        ], (int) $this->query('page', 1));

        $this->json($data);
    }

    public function balances(): void
    {
        $this->requireAuth();
        $this->json($this->kasService->getBalances());
    }

    public function monthlyReport(): void
    {
        $this->requireAuth();
        $this->json($this->reportService->monthly(
            (string) $this->query('kas_type', 'rw'),
            (int) $this->query('year', date('Y')),
            (int) $this->query('month', date('m')),
            $this->query('rt_id', '') === '' ? null : (int) $this->query('rt_id', '')
        ));
    }

    public function yearlyReport(): void
    {
        $this->requireAuth();
        $this->json($this->reportService->yearly(
            (string) $this->query('kas_type', 'rw'),
            (int) $this->query('year', date('Y')),
            $this->query('rt_id', '') === '' ? null : (int) $this->query('rt_id', '')
        ));
    }
}
