<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/LaporanModel.php';

class LaporanController extends Controller
{
    private const PDF_MAX_LINES = 52;

    private LaporanModel $laporanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
    }

    public function index(): void
    {
        $this->requireAuth();

        $moduleOptions = $this->laporanModel->moduleOptions();
        $selectedModule = (string) $this->query('modul', 'penduduk');
        if (!isset($moduleOptions[$selectedModule])) {
            $selectedModule = 'penduduk';
        }

        $filters = $this->filtersFromRequest();
        $rows = $this->laporanModel->reportRows($selectedModule, $filters);
        $summary = $this->laporanModel->summary($filters);

        $this->view('laporan/index', [
            'title'          => 'Laporan - ' . APP_NAME,
            'moduleOptions'  => $moduleOptions,
            'selectedModule' => $selectedModule,
            'selectedLabel'  => $moduleOptions[$selectedModule] ?? ucfirst($selectedModule),
            'rows'           => $rows,
            'summary'        => $summary,
            'filters'        => $filters,
        ]);
    }

    public function export(): void
    {
        $this->requireAuth();

        $format = strtolower((string) $this->query('format', 'pdf'));
        if (!in_array($format, ['pdf', 'excel'], true)) {
            $this->redirect('laporan');
        }

        $moduleOptions = $this->laporanModel->moduleOptions();
        $selectedModule = (string) $this->query('modul', 'penduduk');
        if (!isset($moduleOptions[$selectedModule])) {
            $selectedModule = 'penduduk';
        }

        $filters = $this->filtersFromRequest();
        $rows = $this->laporanModel->reportRows($selectedModule, $filters, 2000);
        $label = $moduleOptions[$selectedModule] ?? ucfirst($selectedModule);

        if ($format === 'excel') {
            $this->exportCsv($label, $rows, $filters);
            return;
        }

        $this->exportPdf($label, $rows, $filters);
    }

    private function filtersFromRequest(): array
    {
        return [
            'rt'      => trim((string) $this->query('rt', '')),
            'tanggal' => trim((string) $this->query('tanggal', '')),
            'bulan'   => (int) $this->query('bulan', 0),
            'tahun'   => (int) $this->query('tahun', 0),
        ];
    }

    private function exportCsv(string $label, array $rows, array $filters): never
    {
        $fileName = 'laporan-' . strtolower(str_replace(' ', '-', $label)) . '-' . date('Ymd-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        $output = fopen('php://output', 'wb');
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, ['Modul', $label]);
        fputcsv($output, ['Filter RT', $filters['rt'] !== '' ? $filters['rt'] : '-']);
        fputcsv($output, ['Filter Tanggal', $filters['tanggal'] !== '' ? $filters['tanggal'] : '-']);
        fputcsv($output, ['Filter Bulan', $filters['bulan'] > 0 ? (string) $filters['bulan'] : '-']);
        fputcsv($output, ['Filter Tahun', $filters['tahun'] > 0 ? (string) $filters['tahun'] : '-']);
        fputcsv($output, ['Diekspor pada', date('Y-m-d H:i:s')]);
        fputcsv($output, []);

        if (!empty($rows)) {
            fputcsv($output, array_keys($rows[0]));
            foreach ($rows as $row) {
                $line = array_map(
                    static fn($value) => is_scalar($value) || $value === null ? (string) ($value ?? '') : json_encode($value),
                    $row
                );
                fputcsv($output, $line);
            }
        } else {
            fputcsv($output, ['Tidak ada data untuk filter terpilih.']);
        }

        fclose($output);
        exit;
    }

    private function exportPdf(string $label, array $rows, array $filters): never
    {
        $fileName = 'laporan-' . strtolower(str_replace(' ', '-', $label)) . '-' . date('Ymd-His') . '.pdf';
        $lines = [
            'Laporan ' . $label,
            'Diekspor pada: ' . date('Y-m-d H:i:s'),
            'Filter RT: ' . ($filters['rt'] !== '' ? $filters['rt'] : '-'),
            'Filter Tanggal: ' . ($filters['tanggal'] !== '' ? $filters['tanggal'] : '-'),
            'Filter Bulan: ' . ($filters['bulan'] > 0 ? (string) $filters['bulan'] : '-'),
            'Filter Tahun: ' . ($filters['tahun'] > 0 ? (string) $filters['tahun'] : '-'),
            '',
        ];

        if (empty($rows)) {
            $lines[] = 'Tidak ada data untuk filter terpilih.';
        } else {
            $headers = array_keys($rows[0]);
            $lines[] = implode(' | ', $headers);
            $lines[] = str_repeat('-', 120);

            foreach ($rows as $row) {
                $values = [];
                foreach ($headers as $header) {
                    $value = $row[$header] ?? '';
                    if (!is_scalar($value) && $value !== null) {
                        $value = json_encode($value);
                    }
                    $text = trim((string) ($value ?? ''));
                    $values[] = mb_substr($text, 0, 30);
                }
                $lines[] = implode(' | ', $values);
                if (count($lines) >= self::PDF_MAX_LINES) {
                    $lines[] = '... data dipotong, gunakan export Excel untuk data lengkap.';
                    break;
                }
            }
        }

        $pdf = $this->simplePdfFromLines($lines);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private function simplePdfFromLines(array $lines): string
    {
        $safeLines = array_map(
            static fn($line) => str_replace(['\\', '(', ')'], ['\\\\', '\(', '\)'], (string) $line),
            $lines
        );

        $content = "BT\n/F1 10 Tf\n50 790 Td\n12 TL\n";
        foreach ($safeLines as $line) {
            $content .= '(' . $line . ") Tj\nT*\n";
        }
        $content .= "ET\n";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Count 1 /Kids [3 0 R] >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $objects[] = "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n{$content}endstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($offsets)) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1, $len = count($offsets); $i < $len; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . count($offsets) . " /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";
        return $pdf;
    }
}
