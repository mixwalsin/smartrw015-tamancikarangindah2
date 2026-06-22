<?php

/**
 * PengaduanModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PengaduanModel extends Model
{
    protected string $table = 'pengaduan';

    public function paginateWithRelations(array $filters = [], int $page = 1, int $perPage = PAGINATION_LIMIT, array $actor = []): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        [$whereSql, $bindings] = $this->buildFilterQuery($filters, $actor);
        $joins = $this->baseJoinSql();

        $countRows = $this->query(
            "SELECT COUNT(*) AS total FROM {$this->table} p {$joins} {$whereSql}",
            $bindings
        );
        $total = (int) ($countRows[0]['total'] ?? 0);

        $sql = "SELECT p.*,
                       u.name AS pelapor_nama,
                       u.email AS pelapor_email,
                       k.name AS kategori_nama,
                       k.slug AS kategori_slug,
                       k.warna AS kategori_warna,
                       k.icon AS kategori_icon,
                       rt.kode AS rt_kode,
                       (SELECT COUNT(*) FROM pengaduan_foto pf WHERE pf.pengaduan_id = p.id) AS total_foto,
                       (SELECT COUNT(*) FROM pengaduan_komentar pk WHERE pk.pengaduan_id = p.id) AS total_komentar
                FROM {$this->table} p
                {$joins}
                {$whereSql}
                ORDER BY p.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->query($sql, $bindings),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
            'keyword' => $filters['keyword'] ?? '',
        ];
    }

    public function findDetail(int $id): array|false
    {
        $rows = $this->query(
            "SELECT p.*, u.name AS pelapor_nama, u.email AS pelapor_email,
                    k.name AS kategori_nama, k.slug AS kategori_slug, k.warna AS kategori_warna, k.icon AS kategori_icon,
                    rt.kode AS rt_kode
             FROM {$this->table} p
             LEFT JOIN users u ON u.id = p.user_id
             LEFT JOIN pengaduan_kategori k ON k.id = p.kategori_id
             LEFT JOIN warga w ON w.id = u.warga_id
             LEFT JOIN kk ON kk.id = w.kk_id
             LEFT JOIN rt ON rt.id = kk.rt_id
             WHERE p.id = ?
             LIMIT 1",
            [$id]
        );

        return $rows[0] ?? false;
    }

    public function updateStatusRecord(int $id, string $status, ?string $keterangan = null, ?string $rejectionReason = null): bool
    {
        return $this->execute(
            "UPDATE {$this->table}
             SET status = ?,
                 rejection_reason = ?,
                 last_status_note = ?,
                 updated_at = ?
             WHERE id = ?",
            [$status, $rejectionReason, $keterangan, date('Y-m-d H:i:s'), $id]
        );
    }

    public function getSummary(array $actor = []): array
    {
        [$whereSql, $bindings] = $this->buildFilterQuery([], $actor);
        $rows = $this->query(
            "SELECT p.status, COUNT(*) AS total
             FROM {$this->table} p
             {$this->baseJoinSql()}
             {$whereSql}
             GROUP BY p.status",
            $bindings
        );

        $summary = [
            'total' => 0,
            'selesai' => 0,
            'diproses' => 0,
            'ditolak' => 0,
        ];

        foreach ($rows as $row) {
            $total = (int) $row['total'];
            $summary['total'] += $total;
            if ($row['status'] === 'selesai') {
                $summary['selesai'] += $total;
            } elseif ($row['status'] === 'ditolak') {
                $summary['ditolak'] += $total;
            } else {
                $summary['diproses'] += $total;
            }
        }

        return $summary;
    }

    public function getCategoryBreakdown(array $actor = []): array
    {
        [$whereSql, $bindings] = $this->buildFilterQuery([], $actor);

        return $this->query(
            "SELECT COALESCE(k.name, 'Tanpa Kategori') AS label, COUNT(*) AS total
             FROM {$this->table} p
             LEFT JOIN pengaduan_kategori k ON k.id = p.kategori_id
             {$this->scopeJoinSql()}
             {$whereSql}
             GROUP BY k.id, k.name
             ORDER BY total DESC",
            $bindings
        );
    }

    public function getTrendByMonth(array $actor = []): array
    {
        [$whereSql, $bindings] = $this->buildFilterQuery([], $actor);

        return $this->query(
            "SELECT DATE_FORMAT(p.created_at, '%Y-%m') AS bulan, COUNT(*) AS total
             FROM {$this->table} p
             {$this->baseJoinSql()}
             {$whereSql}
             GROUP BY DATE_FORMAT(p.created_at, '%Y-%m')
             ORDER BY bulan ASC",
            $bindings
        );
    }

    public function nextTicketSequence(string $period): int
    {
        $rows = $this->query(
            "SELECT MAX(CAST(SUBSTRING_INDEX(no_tiket, '-', -1) AS UNSIGNED)) AS last_sequence
             FROM {$this->table}
             WHERE no_tiket LIKE ?",
            ['PGD-' . $period . '-%']
        );

        return ((int) ($rows[0]['last_sequence'] ?? 0)) + 1;
    }

    public function ticketExists(string $ticket): bool
    {
        $rows = $this->query(
            "SELECT COUNT(*) AS total FROM {$this->table} WHERE no_tiket = ?",
            [$ticket]
        );

        return (int) ($rows[0]['total'] ?? 0) > 0;
    }

    private function buildFilterQuery(array $filters, array $actor): array
    {
        $conditions = [];
        $bindings = [];

        if (($filters['status'] ?? '') !== '') {
            $conditions[] = 'p.status = ?';
            $bindings[] = $filters['status'];
        }

        if (($filters['kategori_id'] ?? '') !== '') {
            $conditions[] = 'p.kategori_id = ?';
            $bindings[] = (int) $filters['kategori_id'];
        }

        if (($filters['prioritas'] ?? '') !== '') {
            $conditions[] = 'p.prioritas = ?';
            $bindings[] = $filters['prioritas'];
        }

        if (($filters['date_from'] ?? '') !== '') {
            $conditions[] = 'DATE(p.created_at) >= ?';
            $bindings[] = $filters['date_from'];
        }

        if (($filters['date_to'] ?? '') !== '') {
            $conditions[] = 'DATE(p.created_at) <= ?';
            $bindings[] = $filters['date_to'];
        }

        if (($filters['keyword'] ?? '') !== '') {
            $conditions[] = '(p.no_tiket LIKE ? OR p.judul LIKE ? OR p.deskripsi LIKE ?)';
            $keyword = '%' . $filters['keyword'] . '%';
            $bindings[] = $keyword;
            $bindings[] = $keyword;
            $bindings[] = $keyword;
        }

        $role = strtolower((string) ($actor['role'] ?? ''));
        if ($role === 'warga' && !empty($actor['id'])) {
            $conditions[] = 'p.user_id = ?';
            $bindings[] = (int) $actor['id'];
        } elseif (in_array($role, ['rt', 'ketua_rt', 'admin_rt'], true) && !empty($actor['rt_id'])) {
            $conditions[] = 'rt.id = ?';
            $bindings[] = (int) $actor['rt_id'];
        }

        $whereSql = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        return [$whereSql, $bindings];
    }

    private function baseJoinSql(): string
    {
        return "LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN pengaduan_kategori k ON k.id = p.kategori_id
                LEFT JOIN warga w ON w.id = u.warga_id
                LEFT JOIN kk ON kk.id = w.kk_id
                LEFT JOIN rt ON rt.id = kk.rt_id";
    }

    private function scopeJoinSql(): string
    {
        return "LEFT JOIN users u ON u.id = p.user_id
                LEFT JOIN warga w ON w.id = u.warga_id
                LEFT JOIN kk ON kk.id = w.kk_id
                LEFT JOIN rt ON rt.id = kk.rt_id";
    }
}
