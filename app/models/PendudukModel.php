<?php

/**
 * PendudukModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PendudukModel extends Model
{
    protected string $table = 'penduduk';

    // ──────────────────────────────────────────
    // Lookup helpers
    // ──────────────────────────────────────────

    public function findByNik(string $nik, ?int $excludeId = null): array|false
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE nik = ? AND id != ? LIMIT 1"
            );
            $stmt->execute([$nik, $excludeId]);
            return $stmt->fetch();
        }
        return $this->findWhere('nik', $nik);
    }

    public function getByRt(string $rt): array
    {
        return $this->where('rt', $rt);
    }

    public function countByJenisKelamin(): array
    {
        return $this->query(
            "SELECT jenis_kelamin, COUNT(*) as total FROM {$this->table} GROUP BY jenis_kelamin"
        );
    }

    public function countByRt(): array
    {
        return $this->query(
            "SELECT rt, COUNT(*) as total FROM {$this->table} GROUP BY rt ORDER BY rt ASC"
        );
    }

    // ──────────────────────────────────────────
    // Paginate with search + filters
    // ──────────────────────────────────────────

    /**
     * Paginate with keyword search and optional filters (rt, jenis_kelamin, usia_min, usia_max).
     *
     * @param int    $page
     * @param string $keyword
     * @param array  $filters  ['rt' => '01', 'jenis_kelamin' => 'L', 'usia_min' => 17, 'usia_max' => 60]
     * @param int    $perPage
     */
    public function paginateWithFilters(
        int    $page,
        string $keyword  = '',
        array  $filters  = [],
        int    $perPage  = PAGINATION_LIMIT
    ): array {
        $offset     = ($page - 1) * $perPage;
        $conditions = [];
        $params     = [];

        if ($keyword !== '') {
            $conditions[] = "(nik LIKE ? OR nama LIKE ? OR no_kk LIKE ? OR alamat LIKE ?)";
            $k = "%{$keyword}%";
            $params       = array_merge($params, [$k, $k, $k, $k]);
        }

        if (!empty($filters['rt'])) {
            $conditions[] = "rt = ?";
            $params[]     = $filters['rt'];
        }

        if (!empty($filters['jenis_kelamin'])) {
            $conditions[] = "jenis_kelamin = ?";
            $params[]     = $filters['jenis_kelamin'];
        }

        if (!empty($filters['status_tinggal'])) {
            $conditions[] = "status_tinggal = ?";
            $params[]     = $filters['status_tinggal'];
        }

        // Age range: filter by tanggal_lahir
        if (!empty($filters['usia_min']) && is_numeric($filters['usia_min'])) {
            $maxDate      = date('Y-m-d', strtotime('-' . (int) $filters['usia_min'] . ' years'));
            $conditions[] = "tanggal_lahir <= ?";
            $params[]     = $maxDate;
        }

        if (!empty($filters['usia_max']) && is_numeric($filters['usia_max'])) {
            $minDate      = date('Y-m-d', strtotime('-' . ((int) $filters['usia_max'] + 1) . ' years + 1 day'));
            $conditions[] = "tanggal_lahir >= ?";
            $params[]     = $minDate;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} {$where} ORDER BY nama ASC LIMIT ? OFFSET ?"
        );

        $i = 1;
        foreach ($params as $v) {
            $stmt->bindValue($i++, $v);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i,   $offset,  PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
            'keyword'      => $keyword,
            'filters'      => $filters,
        ];
    }

    /** BC alias — kept so DashboardController still works. */
    public function paginateWithSearch(int $page, string $keyword = '', int $perPage = PAGINATION_LIMIT): array
    {
        return $this->paginateWithFilters($page, $keyword, [], $perPage);
    }

    // ──────────────────────────────────────────
    // Export helpers
    // ──────────────────────────────────────────

    /**
     * Return all rows (optionally filtered) for export.
     */
    public function getAllForExport(string $keyword = '', array $filters = []): array
    {
        $conditions = [];
        $params     = [];

        if ($keyword !== '') {
            $conditions[] = "(nik LIKE ? OR nama LIKE ? OR no_kk LIKE ? OR alamat LIKE ?)";
            $k = "%{$keyword}%";
            $params       = array_merge($params, [$k, $k, $k, $k]);
        }

        if (!empty($filters['rt'])) {
            $conditions[] = "rt = ?";
            $params[]     = $filters['rt'];
        }

        if (!empty($filters['jenis_kelamin'])) {
            $conditions[] = "jenis_kelamin = ?";
            $params[]     = $filters['jenis_kelamin'];
        }

        if (!empty($filters['status_tinggal'])) {
            $conditions[] = "status_tinggal = ?";
            $params[]     = $filters['status_tinggal'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return $this->query(
            "SELECT * FROM {$this->table} {$where} ORDER BY rt ASC, nama ASC",
            $params
        );
    }

    /**
     * Retrieve selected IDs for export.
     *
     * @param int[] $ids
     */
    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        return $this->query(
            "SELECT * FROM {$this->table} WHERE id IN ({$placeholders}) ORDER BY rt ASC, nama ASC",
            $ids
        );
    }

    // ──────────────────────────────────────────
    // Search (simple)
    // ──────────────────────────────────────────

    public function search(string $keyword): array
    {
        $k = "%{$keyword}%";
        return $this->query(
            "SELECT * FROM {$this->table} WHERE nik LIKE ? OR nama LIKE ? OR alamat LIKE ?",
            [$k, $k, $k]
        );
    }
}
