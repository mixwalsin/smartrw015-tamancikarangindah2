<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/services/PengaduanService.php';

class ReportController extends Controller
{
    private PengaduanService $service;

    public function __construct()
    {
        $this->service = new PengaduanService();
    }

    public function index(): void
    {
        $this->requireAuth();
        $reports = $this->service->reports(authUser() ?? []);

        $this->view('pengaduan/report', [
            'title' => 'Analytics Pengaduan - ' . APP_NAME,
            'report' => $reports,
        ]);
    }

    public function exportExcel(): void
    {
        $this->requireAuth();
        $rows = $this->service->exportRows(authUser() ?? []);

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan-pengaduan-' . date('Ymd-His') . '.xls"');

        echo "<table border='1'><tr><th>No Tiket</th><th>Judul</th><th>Status</th><th>Prioritas</th><th>Pelapor</th><th>Kategori</th><th>Tanggal</th></tr>";
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . e((string) ($row['no_tiket'] ?? '')) . '</td>';
            echo '<td>' . e((string) ($row['judul'] ?? '')) . '</td>';
            echo '<td>' . e((string) ($row['status'] ?? '')) . '</td>';
            echo '<td>' . e((string) ($row['prioritas'] ?? '')) . '</td>';
            echo '<td>' . e((string) ($row['pelapor_nama'] ?? '')) . '</td>';
            echo '<td>' . e((string) ($row['kategori_nama'] ?? '')) . '</td>';
            echo '<td>' . e((string) ($row['created_at'] ?? '')) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }

    public function exportPdf(string $id): void
    {
        $this->requireAuth();

        try {
            $pengaduan = $this->service->findDetail((int) $id, authUser() ?? []);
            if (!$pengaduan) {
                http_response_code(404);
                $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
                return;
            }

            $this->view('pengaduan/pdf', [
                'title' => 'Laporan Pengaduan ' . ($pengaduan['no_tiket'] ?? $pengaduan['id']),
                'pengaduan' => $pengaduan,
            ], null);
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
            $this->redirect('pengaduan');
        }
    }
}
