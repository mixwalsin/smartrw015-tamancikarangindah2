<?php

declare(strict_types=1);

class LaporanController extends Controller
{
    private array $modules = [
        'penduduk' => ['title' => 'Data Penduduk', 'sql' => 'SELECT w.nik, w.nama, rt.kode AS rt, kk.no_kk, kk.alamat FROM warga w LEFT JOIN kk ON kk.id = w.kk_id LEFT JOIN rt ON rt.id = kk.rt_id ORDER BY w.nama ASC'],
        'kk' => ['title' => 'Kartu Keluarga', 'sql' => 'SELECT kk.no_kk, rt.kode AS rt, kk.alamat FROM kk LEFT JOIN rt ON rt.id = kk.rt_id ORDER BY kk.no_kk ASC'],
        'kas_rw' => ['title' => 'Kas RW', 'sql' => 'SELECT tanggal, jenis, kategori, jumlah, saldo_setelah FROM kas_rw ORDER BY tanggal DESC'],
        'kas_rt' => ['title' => 'Kas RT', 'sql' => 'SELECT rt.kode AS rt, kas_rt.tanggal, kas_rt.jenis, kas_rt.kategori, kas_rt.jumlah, kas_rt.saldo_setelah FROM kas_rt LEFT JOIN rt ON rt.id = kas_rt.rt_id ORDER BY kas_rt.tanggal DESC'],
        'surat' => ['title' => 'Pengajuan Surat', 'sql' => 'SELECT surat.nama AS jenis_surat, warga.nama AS pemohon, pengajuan_surat.status, pengajuan_surat.keperluan FROM pengajuan_surat INNER JOIN surat ON surat.id = pengajuan_surat.surat_id INNER JOIN warga ON warga.id = pengajuan_surat.warga_id ORDER BY pengajuan_surat.created_at DESC'],
        'pengaduan' => ['title' => 'Pengaduan', 'sql' => 'SELECT warga.nama AS pelapor, pengaduan.kategori, pengaduan.judul, pengaduan.status FROM pengaduan LEFT JOIN warga ON warga.id = pengaduan.warga_id ORDER BY pengaduan.created_at DESC'],
        'umkm' => ['title' => 'UMKM', 'sql' => 'SELECT nama_usaha, kategori, no_hp, status FROM umkm ORDER BY nama_usaha ASC'],
        'posyandu' => ['title' => 'Posyandu', 'sql' => 'SELECT tanggal, jenis_kegiatan, status_gizi, berat_badan, tinggi_badan FROM posyandu ORDER BY tanggal DESC'],
    ];

    public function index(): void
    {
        $this->requireAuth();
        $module = (string) $this->query('module', 'penduduk');
        $module = array_key_exists($module, $this->modules) ? $module : 'penduduk';
        $rows = (new GenericTableModel('log_aktivitas'))->query($this->modules[$module]['sql']);

        $this->view('laporan/index', [
            'title' => 'Laporan - ' . APP_NAME,
            'module' => $module,
            'modules' => $this->modules,
            'rows' => $rows,
        ]);
    }

    public function export(): void
    {
        $this->requireAuth();
        $module = (string) $this->query('module', 'penduduk');
        $format = (string) $this->query('format', 'excel');
        $module = array_key_exists($module, $this->modules) ? $module : 'penduduk';
        $rows = (new GenericTableModel('log_aktivitas'))->query($this->modules[$module]['sql']);

        if ($format === 'pdf') {
            $this->view('shared/report_pdf', [
                'title' => $this->modules[$module]['title'],
                'rows' => $rows,
            ], null);
            return;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan-' . $module . '-' . date('Ymd-His') . '.csv"');
        $output = fopen('php://output', 'wb');
        if ($output === false) {
            error_log('Gagal membuka php://output untuk ekspor laporan ' . $module);
            http_response_code(500);
            echo 'Gagal menyiapkan file laporan.';
            exit;
        }
        if ($rows !== []) {
            fputcsv($output, array_keys($rows[0]));
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
        }
        fclose($output);
        exit;
    }
}
