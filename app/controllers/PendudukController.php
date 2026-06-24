<?php

/**
 * PendudukController
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Handles full CRUD, photo upload, import (CSV/Excel), export (CSV, Excel, PDF).
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/PendudukModel.php';

class PendudukController extends Controller
{
    private PendudukModel $model;

    // Allowed image types for foto upload
    private const FOTO_TYPES = ['jpg', 'jpeg', 'png', 'webp'];

    // Allowed types for import
    private const IMPORT_TYPES = ['csv', 'xls', 'xlsx'];

    public function __construct()
    {
        $this->model = new PendudukModel();
    }

    // ──────────────────────────────────────────
    // Index — DataTables view
    // ──────────────────────────────────────────

    public function index(): void
    {
        $this->requireAuth();

        $page    = max(1, (int) $this->query('page', 1));
        $keyword = trim((string) $this->query('keyword', ''));
        $filters = [
            'rt'             => $this->query('rt', ''),
            'jenis_kelamin'  => $this->query('jenis_kelamin', ''),
            'status_tinggal' => $this->query('status_tinggal', ''),
            'usia_min'       => $this->query('usia_min', ''),
            'usia_max'       => $this->query('usia_max', ''),
        ];

        $pagination = $this->model->paginateWithFilters($page, $keyword, $filters);

        $this->view('penduduk/index', [
            'title'      => 'Data Penduduk - ' . APP_NAME,
            'pagination' => $pagination,
            'filters'    => $filters,
        ]);
    }

    // ──────────────────────────────────────────
    // Create & Store
    // ──────────────────────────────────────────

    public function create(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $this->view('penduduk/create', [
            'title' => 'Tambah Penduduk - ' . APP_NAME,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('penduduk/create');
        }

        $data = $this->collectFormData();

        // Validasi wajib
        if ($data['nik'] === '' || $data['nama'] === '') {
            setFlash('error', 'NIK dan Nama wajib diisi.');
            $this->redirect('penduduk/create');
        }

        // Validasi NIK unik
        if ($this->model->findByNik($data['nik'])) {
            setFlash('error', 'NIK sudah terdaftar.');
            $this->redirect('penduduk/create');
        }

        // Validasi format NIK
        if (!preg_match('/^\d{16}$/', $data['nik'])) {
            setFlash('error', 'NIK harus terdiri dari 16 digit angka.');
            $this->redirect('penduduk/create');
        }

        // Validasi email jika diisi
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Format email tidak valid.');
            $this->redirect('penduduk/create');
        }

        // Upload foto
        if (!empty($_FILES['foto']['name'])) {
            try {
                $data['foto'] = $this->uploadFoto($_FILES['foto']);
            } catch (RuntimeException $e) {
                setFlash('error', $e->getMessage());
                $this->redirect('penduduk/create');
            }
        }

        $this->model->insert($data);
        setFlash('success', 'Data penduduk berhasil ditambahkan.');
        $this->redirect('penduduk');
    }

    // ──────────────────────────────────────────
    // Show (Detail)
    // ──────────────────────────────────────────

    public function show(string $id): void
    {
        $this->requireAuth();
        $penduduk = $this->model->find((int) $id);
        if (!$penduduk) {
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Tidak Ditemukan']);
            return;
        }
        $this->view('penduduk/show', [
            'title'    => 'Detail Penduduk - ' . APP_NAME,
            'penduduk' => $penduduk,
        ]);
    }

    // ──────────────────────────────────────────
    // Edit & Update
    // ──────────────────────────────────────────

    public function edit(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $penduduk = $this->model->find((int) $id);
        if (!$penduduk) {
            $this->redirect('penduduk');
        }
        $this->view('penduduk/edit', [
            'title'    => 'Edit Penduduk - ' . APP_NAME,
            'penduduk' => $penduduk,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('penduduk/edit/' . $id);
        }

        $existing = $this->model->find((int) $id);
        if (!$existing) {
            $this->redirect('penduduk');
        }

        $data = $this->collectFormData();

        // Validasi wajib
        if ($data['nik'] === '' || $data['nama'] === '') {
            setFlash('error', 'NIK dan Nama wajib diisi.');
            $this->redirect('penduduk/edit/' . $id);
        }

        // Validasi format NIK
        if (!preg_match('/^\d{16}$/', $data['nik'])) {
            setFlash('error', 'NIK harus terdiri dari 16 digit angka.');
            $this->redirect('penduduk/edit/' . $id);
        }

        // Validasi NIK unik (kecuali record ini sendiri)
        if ($this->model->findByNik($data['nik'], (int) $id)) {
            setFlash('error', 'NIK sudah digunakan oleh penduduk lain.');
            $this->redirect('penduduk/edit/' . $id);
        }

        // Validasi email jika diisi
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Format email tidak valid.');
            $this->redirect('penduduk/edit/' . $id);
        }

        // Upload foto baru (opsional)
        if (!empty($_FILES['foto']['name'])) {
            try {
                $newFoto    = $this->uploadFoto($_FILES['foto']);
                // Hapus foto lama
                if (!empty($existing['foto'])) {
                    $oldPath = UPLOAD_PATH . '/foto/' . $existing['foto'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $data['foto'] = $newFoto;
            } catch (RuntimeException $e) {
                setFlash('error', $e->getMessage());
                $this->redirect('penduduk/edit/' . $id);
            }
        } else {
            // Pertahankan foto lama
            $data['foto'] = $existing['foto'];
        }

        $this->model->update((int) $id, $data);
        setFlash('success', 'Data penduduk berhasil diperbarui.');
        $this->redirect('penduduk');
    }

    // ──────────────────────────────────────────
    // Delete
    // ──────────────────────────────────────────

    public function delete(string $id): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('penduduk');
        }

        $existing = $this->model->find((int) $id);
        if ($existing && !empty($existing['foto'])) {
            $path = UPLOAD_PATH . '/foto/' . $existing['foto'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $this->model->delete((int) $id);
        setFlash('success', 'Data penduduk berhasil dihapus.');
        $this->redirect('penduduk');
    }

    // ──────────────────────────────────────────
    // Import
    // ──────────────────────────────────────────

    public function import(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw');
        $this->view('penduduk/import', [
            'title' => 'Import Data Penduduk - ' . APP_NAME,
        ]);
    }

    public function processImport(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw');

        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('penduduk/import');
        }

        if (empty($_FILES['file']['name'])) {
            setFlash('error', 'Pilih file untuk diimport.');
            $this->redirect('penduduk/import');
        }

        $file = $_FILES['file'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, self::IMPORT_TYPES, true)) {
            setFlash('error', 'Format file tidak didukung. Gunakan CSV atau Excel (.xls/.xlsx).');
            $this->redirect('penduduk/import');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'Gagal mengupload file.');
            $this->redirect('penduduk/import');
        }

        try {
            $rows = $this->parseImportFile($file['tmp_name'], $ext);
            [$imported, $skipped, $errors] = $this->importRows($rows);

            $msg = "Import selesai: {$imported} data berhasil diimpor, {$skipped} dilewati (duplikat NIK).";
            if ($errors > 0) {
                $msg .= " {$errors} baris gagal diproses.";
            }
            setFlash('success', $msg);
        } catch (RuntimeException $e) {
            setFlash('error', 'Import gagal: ' . $e->getMessage());
        }

        $this->redirect('penduduk');
    }

    // ──────────────────────────────────────────
    // Export
    // ──────────────────────────────────────────

    public function exportExcel(): void
    {
        $this->requireAuth();

        $keyword  = trim((string) $this->query('keyword', ''));
        $filters  = [
            'rt'             => $this->query('rt', ''),
            'jenis_kelamin'  => $this->query('jenis_kelamin', ''),
            'status_tinggal' => $this->query('status_tinggal', ''),
        ];
        $idsParam = $this->query('ids', '');
        $ids      = $idsParam !== '' ? array_map('intval', explode(',', $idsParam)) : [];

        $data = $ids ? $this->model->getByIds($ids) : $this->model->getAllForExport($keyword, $filters);

        $filename = 'data_penduduk_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // BOM untuk Excel
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        fputcsv($out, [
            'No', 'NIK', 'No KK', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir',
            'Jenis Kelamin', 'Agama', 'Status Kawin', 'Pendidikan', 'Pekerjaan',
            'Alamat', 'RT', 'RW', 'No Rumah', 'No HP', 'Email', 'Status Tinggal',
        ]);

        $no = 1;
        foreach ($data as $row) {
            fputcsv($out, [
                $no++,
                $row['nik'],
                $row['no_kk'] ?? '',
                $row['nama'],
                $row['tempat_lahir'] ?? '',
                $row['tanggal_lahir'] ?? '',
                $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : ($row['jenis_kelamin'] === 'P' ? 'Perempuan' : ''),
                $row['agama'] ?? '',
                $row['status_kawin'] ?? '',
                $row['pendidikan'] ?? '',
                $row['pekerjaan'] ?? '',
                $row['alamat'] ?? '',
                $row['rt'] ?? '',
                $row['rw'] ?? '',
                $row['no_rumah'] ?? '',
                $row['no_hp'] ?? '',
                $row['email'] ?? '',
                $row['status_tinggal'] ?? '',
            ]);
        }
        fclose($out);
        exit;
    }

    public function exportPdf(): void
    {
        $this->requireAuth();

        $keyword  = trim((string) $this->query('keyword', ''));
        $filters  = [
            'rt'             => $this->query('rt', ''),
            'jenis_kelamin'  => $this->query('jenis_kelamin', ''),
            'status_tinggal' => $this->query('status_tinggal', ''),
        ];
        $idsParam = $this->query('ids', '');
        $ids      = $idsParam !== '' ? array_map('intval', explode(',', $idsParam)) : [];

        $data = $ids ? $this->model->getByIds($ids) : $this->model->getAllForExport($keyword, $filters);

        $this->view('penduduk/export_pdf', [
            'title'  => 'Export PDF Data Penduduk',
            'data'   => $data,
            'tanggal' => date('d F Y'),
        ], null); // null = no layout, raw HTML for print
    }

    // ──────────────────────────────────────────
    // Download template import
    // ──────────────────────────────────────────

    public function importTemplate(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw');

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="template_import_penduduk.csv"');
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        fputcsv($out, [
            'NIK', 'No KK', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)',
            'Jenis Kelamin (L/P)', 'Agama', 'Status Kawin', 'Pendidikan', 'Pekerjaan',
            'Alamat', 'RT', 'No Rumah', 'No HP', 'Email', 'Status Tinggal',
        ]);
        // Contoh baris
        fputcsv($out, [
            '3216031234567890', '3216031234560001', 'Budi Santoso', 'Bekasi', '1990-01-15',
            'L', 'Islam', 'Kawin', 'S1', 'Karyawan Swasta',
            'Jl. Contoh No. 1', '01', '1A', '081234567890', 'budi@email.com', 'Tetap',
        ]);
        fclose($out);
        exit;
    }

    // ──────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────

    private function collectFormData(): array
    {
        return [
            'nik'            => trim($this->input('nik', '')),
            'no_kk'          => trim($this->input('no_kk', '')),
            'nama'           => trim($this->input('nama', '')),
            'tempat_lahir'   => trim($this->input('tempat_lahir', '')),
            'tanggal_lahir'  => $this->input('tanggal_lahir', '') ?: null,
            'jenis_kelamin'  => $this->input('jenis_kelamin', '') ?: null,
            'agama'          => $this->input('agama', '') ?: null,
            'status_kawin'   => $this->input('status_kawin', '') ?: null,
            'pendidikan'     => $this->input('pendidikan', '') ?: null,
            'pekerjaan'      => trim($this->input('pekerjaan', '')),
            'alamat'         => trim($this->input('alamat', '')),
            'rt'             => $this->input('rt', '') ?: null,
            'rw'             => '015',
            'no_rumah'       => trim($this->input('no_rumah', '')),
            'no_hp'          => trim($this->input('no_hp', '')),
            'email'          => strtolower(trim($this->input('email', ''))),
            'status_tinggal' => $this->input('status_tinggal', 'Tetap'),
        ];
    }

    private function uploadFoto(array $file): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload foto gagal (kode: ' . $file['error'] . ').');
        }

        $maxSize = 2 * 1024 * 1024; // 2 MB
        if ($file['size'] > $maxSize) {
            throw new RuntimeException('Ukuran foto maksimal 2 MB.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::FOTO_TYPES, true)) {
            throw new RuntimeException('Format foto tidak didukung. Gunakan JPG, PNG, atau WEBP.');
        }

        $dir = UPLOAD_PATH . '/foto';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new RuntimeException('Gagal membuat direktori upload foto.');
        }

        $filename = uniqid('foto_', true) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
            throw new RuntimeException('Gagal menyimpan foto.');
        }

        return $filename;
    }

    /**
     * Parse uploaded CSV/XLS/XLSX file and return array of associative rows.
     */
    private function parseImportFile(string $tmpPath, string $ext): array
    {
        if ($ext === 'csv') {
            return $this->parseCsv($tmpPath);
        }

        // For XLS/XLSX: attempt simple XML parsing (xlsx) or fallback CSV
        if ($ext === 'xlsx') {
            return $this->parseXlsx($tmpPath);
        }

        // XLS: not easily parseable without a library; instruct user to save as CSV
        throw new RuntimeException('Format XLS tidak didukung secara langsung. Silakan simpan sebagai CSV atau XLSX terlebih dahulu.');
    }

    private function parseCsv(string $path): array
    {
        $rows   = [];
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new RuntimeException('Tidak dapat membaca file CSV.');
        }

        // Detect BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = null;
        while (($line = fgetcsv($handle, 0, ',')) !== false) {
            if ($headers === null) {
                $headers = $line;
                continue;
            }
            if (count($line) < 2) {
                continue;
            }
            $rows[] = array_combine(
                array_slice($headers, 0, count($line)),
                array_pad($line, count($headers), '')
            );
        }
        fclose($handle);
        return $rows;
    }

    /**
     * Minimal XLSX reader using ZIP + XML (no external library).
     */
    private function parseXlsx(string $path): array
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('Ekstensi ZIP PHP tidak tersedia. Gunakan format CSV.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('File XLSX tidak dapat dibuka.');
        }

        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml !== false) {
            $dom = new DOMDocument();
            @$dom->loadXML($ssXml);
            foreach ($dom->getElementsByTagName('si') as $si) {
                $sharedStrings[] = $si->textContent;
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('Sheet pertama tidak ditemukan dalam file XLSX.');
        }

        $dom = new DOMDocument();
        @$dom->loadXML($sheetXml);

        $matrix  = [];
        foreach ($dom->getElementsByTagName('row') as $rowNode) {
            $rowData = [];
            foreach ($rowNode->getElementsByTagName('c') as $cell) {
                $t   = $cell->getAttribute('t');
                $vEl = $cell->getElementsByTagName('v')->item(0);
                $val = $vEl ? $vEl->textContent : '';
                if ($t === 's') {
                    $val = $sharedStrings[(int) $val] ?? '';
                }
                $rowData[] = $val;
            }
            $matrix[] = $rowData;
        }

        if (empty($matrix)) {
            return [];
        }

        $headers = array_shift($matrix);
        $rows    = [];
        foreach ($matrix as $line) {
            if (count($line) < 2) {
                continue;
            }
            $rows[] = array_combine(
                array_slice($headers, 0, count($line)),
                array_pad($line, count($headers), '')
            );
        }
        return $rows;
    }

    /**
     * Insert parsed rows into DB.
     *
     * @return array{int, int, int} [imported, skipped, errors]
     */
    private function importRows(array $rows): array
    {
        $imported = 0;
        $skipped  = 0;
        $errors   = 0;

        // Map flexible column headers (case-insensitive, strip spaces)
        $colMap = [
            'nik'            => ['nik'],
            'no_kk'          => ['no kk', 'nokk', 'no_kk'],
            'nama'           => ['nama lengkap', 'nama'],
            'tempat_lahir'   => ['tempat lahir', 'tempat_lahir'],
            'tanggal_lahir'  => ['tanggal lahir (yyyy-mm-dd)', 'tanggal lahir', 'tanggal_lahir', 'tgl lahir'],
            'jenis_kelamin'  => ['jenis kelamin (l/p)', 'jenis kelamin', 'jenis_kelamin', 'jk'],
            'agama'          => ['agama'],
            'status_kawin'   => ['status kawin', 'status_kawin', 'status perkawinan'],
            'pendidikan'     => ['pendidikan'],
            'pekerjaan'      => ['pekerjaan'],
            'alamat'         => ['alamat'],
            'rt'             => ['rt'],
            'no_rumah'       => ['no rumah', 'no_rumah', 'nomor rumah'],
            'no_hp'          => ['no hp', 'no_hp', 'nomor hp', 'telepon', 'hp'],
            'email'          => ['email'],
            'status_tinggal' => ['status tinggal', 'status_tinggal'],
        ];

        foreach ($rows as $row) {
            try {
                // Normalise row keys
                $normalized = [];
                foreach ($row as $k => $v) {
                    $normalized[strtolower(trim($k))] = trim((string) $v);
                }

                // Map to DB fields
                $rec = [];
                foreach ($colMap as $field => $aliases) {
                    $rec[$field] = '';
                    foreach ($aliases as $alias) {
                        if (array_key_exists($alias, $normalized)) {
                            $rec[$field] = $normalized[$alias];
                            break;
                        }
                    }
                }

                $nik = preg_replace('/\D/', '', $rec['nik']);
                if (strlen($nik) !== 16) {
                    $errors++;
                    continue;
                }

                if ($this->model->findByNik($nik)) {
                    $skipped++;
                    continue;
                }

                $jk = strtoupper(substr($rec['jenis_kelamin'], 0, 1));
                if (!in_array($jk, ['L', 'P'], true)) {
                    $jk = null;
                }

                $tgl = $rec['tanggal_lahir'] ?: null;
                if ($tgl && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) {
                    $parsed = date_create_from_format('d/m/Y', $tgl)
                           ?: date_create_from_format('d-m-Y', $tgl)
                           ?: date_create($tgl);
                    $tgl = $parsed ? $parsed->format('Y-m-d') : null;
                }

                $validAgama    = ['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'];
                $validKawin    = ['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'];
                $validPendidikan = ['Tidak Sekolah','SD','SMP','SMA/SMK','D1','D2','D3','S1','S2','S3'];
                $validTinggal  = ['Tetap','Pendatang','Pindah','Meninggal'];

                $this->model->insert([
                    'nik'            => $nik,
                    'no_kk'          => preg_replace('/\D/', '', $rec['no_kk']) ?: null,
                    'nama'           => $rec['nama'],
                    'tempat_lahir'   => $rec['tempat_lahir'] ?: null,
                    'tanggal_lahir'  => $tgl,
                    'jenis_kelamin'  => $jk,
                    'agama'          => in_array($rec['agama'], $validAgama, true) ? $rec['agama'] : null,
                    'status_kawin'   => in_array($rec['status_kawin'], $validKawin, true) ? $rec['status_kawin'] : null,
                    'pendidikan'     => in_array($rec['pendidikan'], $validPendidikan, true) ? $rec['pendidikan'] : null,
                    'pekerjaan'      => $rec['pekerjaan'] ?: null,
                    'alamat'         => $rec['alamat'] ?: null,
                    'rt'             => $rec['rt'] ? str_pad($rec['rt'], 2, '0', STR_PAD_LEFT) : null,
                    'rw'             => '015',
                    'no_rumah'       => $rec['no_rumah'] ?: null,
                    'no_hp'          => $rec['no_hp'] ?: null,
                    'email'          => $rec['email'] ?: null,
                    'status_tinggal' => in_array($rec['status_tinggal'], $validTinggal, true)
                                        ? $rec['status_tinggal'] : 'Tetap',
                ]);
                $imported++;
            } catch (\Throwable) {
                $errors++;
            }
        }

        return [$imported, $skipped, $errors];
    }
}
