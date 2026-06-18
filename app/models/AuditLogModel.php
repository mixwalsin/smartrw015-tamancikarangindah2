<?php

/**
 * AuditLogModel – wrapper untuk tabel log_aktivitas
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class AuditLogModel extends Model
{
    protected string $table = 'log_aktivitas';

    /**
     * Catat aktivitas pengguna.
     */
    public function log(
        ?int   $userId,
        string $aksi,
        string $modul,
        ?int   $dataId      = null,
        string $keterangan  = ''
    ): void {
        $this->execute(
            "INSERT INTO {$this->table}
             (user_id, aksi, modul, data_id, keterangan, ip_address, user_agent, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $userId,
                $aksi,
                $modul,
                $dataId,
                $keterangan,
                $_SERVER['REMOTE_ADDR']    ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                date('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * Ambil log dengan filter dan paginasi.
     */
    public function paginated(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['modul'])) {
            $where[]  = 'la.modul = ?';
            $params[] = $filters['modul'];
        }
        if (!empty($filters['aksi'])) {
            $where[]  = 'la.aksi = ?';
            $params[] = $filters['aksi'];
        }
        if (!empty($filters['user_id'])) {
            $where[]  = 'la.user_id = ?';
            $params[] = (int) $filters['user_id'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset      = ($page - 1) * $perPage;

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} la {$whereClause}"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $dataParams   = [...$params, $perPage, $offset];
        $dataStmt     = $this->db->prepare("
            SELECT la.*, u.name AS user_name, u.username
            FROM {$this->table} la
            LEFT JOIN users u ON u.id = la.user_id
            {$whereClause}
            ORDER BY la.id DESC
            LIMIT ? OFFSET ?
        ");
        $dataStmt->execute($dataParams);

        return [
            'data'         => $dataStmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Ambil daftar modul yang ada di log.
     */
    public function getModuls(): array
    {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT modul FROM {$this->table} ORDER BY modul ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
