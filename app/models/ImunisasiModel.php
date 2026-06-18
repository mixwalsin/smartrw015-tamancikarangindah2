<?php

/**
 * ImunisasiModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class ImunisasiModel extends Model
{
    protected string $table = 'imunisasi';

    /**
     * Ambil semua imunisasi beserta nama balita
     */
    public function allWithBalita(int $page = 1, int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} i JOIN balita b ON i.balita_id = b.id"
        );
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT i.*, b.nama AS nama_balita
             FROM {$this->table} i
             JOIN balita b ON i.balita_id = b.id
             ORDER BY i.tanggal_imunisasi DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    public function getByBalita(int $balitaId): array
    {
        return $this->query(
            "SELECT * FROM {$this->table}
             WHERE balita_id = ?
             ORDER BY tanggal_imunisasi ASC",
            [$balitaId]
        );
    }
}
