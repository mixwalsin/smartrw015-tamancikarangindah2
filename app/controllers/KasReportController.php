<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/KasReportService.php';

class KasReportController extends Controller
{
    private KasReportService $service;

    public function __construct()
    {
        $this->service = new KasReportService();
    }

    public function monthly(): void
    {
        $this->requireAuth();

        $kasType = (string) $this->query('kas_type', 'rw');
        $year = (int) $this->query('year', date('Y'));
        $month = (int) $this->query('month', date('m'));
        $rtIdRaw = $this->query('rt_id', '');
        $rtId = $rtIdRaw === '' ? null : (int) $rtIdRaw;

        $report = $this->service->monthly($kasType, $year, $month, $rtId);

        $this->view('keuangan/report_monthly', [
            'title' => 'Laporan Bulanan Kas - ' . APP_NAME,
            'report' => $report,
        ]);
    }

    public function yearly(): void
    {
        $this->requireAuth();

        $kasType = (string) $this->query('kas_type', 'rw');
        $year = (int) $this->query('year', date('Y'));
        $rtIdRaw = $this->query('rt_id', '');
        $rtId = $rtIdRaw === '' ? null : (int) $rtIdRaw;

        $report = $this->service->yearly($kasType, $year, $rtId);

        $this->view('keuangan/report_yearly', [
            'title' => 'Laporan Tahunan Kas - ' . APP_NAME,
            'report' => $report,
        ]);
    }

    public function exportPdf(): void
    {
        $this->requireAuth();

        $kasType = (string) $this->query('kas_type', 'rw');
        $year = (int) $this->query('year', date('Y'));
        $month = (int) $this->query('month', date('m'));

        $report = $this->service->monthly($kasType, $year, $month, null);
        $html = $this->service->reportHtml(
            'Laporan Kas ' . strtoupper($kasType) . ' ' . $month . '/' . $year,
            $report['items'],
            $report
        );

        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="laporan-kas-' . $kasType . '-' . $year . '-' . $month . '.html"');
        echo $html;
        exit;
    }

    public function exportExcel(): void
    {
        $this->requireAuth();

        $kasType = (string) $this->query('kas_type', 'rw');
        $year = (int) $this->query('year', date('Y'));
        $month = (int) $this->query('month', date('m'));

        $report = $this->service->monthly($kasType, $year, $month, null);
        $csv = $this->service->exportCsv($report['items']);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan-kas-' . $kasType . '-' . $year . '-' . $month . '.csv"');
        echo $csv;
        exit;
    }

    public function print(): void
    {
        $this->requireAuth();

        $kasType = (string) $this->query('kas_type', 'rw');
        $year = (int) $this->query('year', date('Y'));
        $month = (int) $this->query('month', date('m'));

        $report = $this->service->monthly($kasType, $year, $month, null);

        $this->view('keuangan/print', [
            'title' => 'Cetak Laporan Kas - ' . APP_NAME,
            'report' => $report,
        ], null);
    }
}
