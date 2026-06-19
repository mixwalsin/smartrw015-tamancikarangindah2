<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class LaporanModel extends Model
{
    protected string $table = '';

    private array $modules = [
        'penduduk' => ['label' => 'Penduduk', 'tables' => ['penduduk', 'warga']],
        'kk'       => ['label' => 'KK', 'tables' => ['kk']],
        'kas_rw'   => ['label' => 'Kas RW', 'tables' => ['kas_rw', 'keuangan']],
        'kas_rt'   => ['label' => 'Kas RT', 'tables' => ['kas_rt']],
        'surat'    => ['label' => 'Surat', 'tables' => ['pengajuan_surat', 'surat_pengajuan']],
        'pengaduan'=> ['label' => 'Pengaduan', 'tables' => ['pengaduan']],
        'umkm'     => ['label' => 'UMKM', 'tables' => ['umkm']],
        'posyandu' => ['label' => 'Posyandu', 'tables' => ['posyandu']],
    ];

    private array $tableExistsCache = [];
    private array $columnExistsCache = [];
    private array $firstColumnCache = [];

    public function moduleOptions(): array
    {
        $options = [];
        foreach ($this->modules as $key => $module) {
            $options[$key] = $module['label'];
        }
        return $options;
    }

    public function moduleExists(string $module): bool
    {
        return isset($this->modules[$module]);
    }

    public function summary(array $filters): array
    {
        $summary = [];
        foreach ($this->modules as $module => $meta) {
            $summary[$module] = [
                'label' => $meta['label'],
                'total' => $this->countRows($module, $filters),
            ];
        }
        return $summary;
    }

    public function reportRows(string $module, array $filters, int $limit = 500): array
    {
        $table = $this->resolveTable($module);
        if ($table === null) {
            return [];
        }

        $whereParts = [];
        $params = [];
        $this->applyRtFilter($table, $filters['rt'] ?? '', $whereParts, $params);
        $this->applyDateFilters($table, $filters, $whereParts, $params);

        $whereSql = $whereParts ? (' WHERE ' . implode(' AND ', $whereParts)) : '';
        $orderBy = $this->orderByColumn($table);

        $sql = "SELECT * FROM `{$table}`{$whereSql} ORDER BY {$orderBy} DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);

        $index = 1;
        foreach ($params as $param) {
            $stmt->bindValue($index++, $param);
        }
        $stmt->bindValue($index, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function countRows(string $module, array $filters): int
    {
        $table = $this->resolveTable($module);
        if ($table === null) {
            return 0;
        }

        $whereParts = [];
        $params = [];
        $this->applyRtFilter($table, $filters['rt'] ?? '', $whereParts, $params);
        $this->applyDateFilters($table, $filters, $whereParts, $params);

        $whereSql = $whereParts ? (' WHERE ' . implode(' AND ', $whereParts)) : '';
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$table}`{$whereSql}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function resolveTable(string $module): ?string
    {
        if (!isset($this->modules[$module])) {
            return null;
        }

        foreach ($this->modules[$module]['tables'] as $table) {
            if ($this->tableExists($table)) {
                return $table;
            }
        }
        return null;
    }

    private function tableExists(string $table): bool
    {
        if (array_key_exists($table, $this->tableExistsCache)) {
            return $this->tableExistsCache[$table];
        }

        $stmt = $this->db->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        $exists = (bool) $stmt->fetchColumn();
        $this->tableExistsCache[$table] = $exists;
        return $exists;
    }

    private function columnExists(string $table, string $column): bool
    {
        $key = $table . '.' . $column;
        if (array_key_exists($key, $this->columnExistsCache)) {
            return $this->columnExistsCache[$key];
        }

        $stmt = $this->db->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        $exists = (bool) $stmt->fetchColumn();
        $this->columnExistsCache[$key] = $exists;
        return $exists;
    }

    private function applyRtFilter(string $table, string $rt, array &$whereParts, array &$params): void
    {
        $rt = trim($rt);
        if ($rt === '') {
            return;
        }

        $rtNumber = (int) preg_replace('/[^0-9]/', '', $rt);
        if ($rtNumber <= 0) {
            return;
        }

        $rtTwo = str_pad((string) $rtNumber, 2, '0', STR_PAD_LEFT);
        $rtThree = str_pad((string) $rtNumber, 3, '0', STR_PAD_LEFT);

        if ($this->columnExists($table, 'rt')) {
            $whereParts[] = '`rt` IN (?, ?, ?)';
            $params[] = $rtTwo;
            $params[] = $rtThree;
            $params[] = (string) $rtNumber;
            return;
        }

        if ($this->columnExists($table, 'rt_text')) {
            $whereParts[] = '`rt_text` IN (?, ?, ?)';
            $params[] = $rtTwo;
            $params[] = $rtThree;
            $params[] = (string) $rtNumber;
            return;
        }

        if ($this->columnExists($table, 'rt_id')) {
            $whereParts[] = '`rt_id` = ?';
            $params[] = $rtNumber;
        }
    }

    private function applyDateFilters(string $table, array $filters, array &$whereParts, array &$params): void
    {
        $dateColumn = $this->dateColumn($table);
        if ($dateColumn === null) {
            return;
        }

        $tanggal = trim((string) ($filters['tanggal'] ?? ''));
        if ($tanggal !== '') {
            $whereParts[] = "DATE(`{$dateColumn}`) = ?";
            $params[] = $tanggal;
        }

        $bulan = (int) ($filters['bulan'] ?? 0);
        if ($bulan >= 1 && $bulan <= 12) {
            $whereParts[] = "MONTH(`{$dateColumn}`) = ?";
            $params[] = $bulan;
        }

        $tahun = (int) ($filters['tahun'] ?? 0);
        if ($tahun > 0) {
            $whereParts[] = "YEAR(`{$dateColumn}`) = ?";
            $params[] = $tahun;
        }
    }

    private function dateColumn(string $table): ?string
    {
        foreach (['tanggal', 'created_at'] as $column) {
            if ($this->columnExists($table, $column)) {
                return $column;
            }
        }
        return null;
    }

    private function orderByColumn(string $table): string
    {
        foreach (['tanggal', 'created_at', 'id'] as $column) {
            if ($this->columnExists($table, $column)) {
                return "`{$column}`";
            }
        }
        $firstColumn = $this->firstColumn($table);
        return $firstColumn !== null ? "`{$firstColumn}`" : '(SELECT 1)';
    }

    private function firstColumn(string $table): ?string
    {
        if (array_key_exists($table, $this->firstColumnCache)) {
            return $this->firstColumnCache[$table];
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM `{$table}`");
        $row = $stmt->fetch();
        $column = is_array($row) ? ($row['Field'] ?? null) : null;
        $this->firstColumnCache[$table] = is_string($column) ? $column : null;
        return $this->firstColumnCache[$table];
    }
}
