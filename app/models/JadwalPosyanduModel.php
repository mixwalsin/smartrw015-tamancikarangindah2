<?php

/**
 * JadwalPosyanduModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class JadwalPosyanduModel extends Model
{
    protected string $table = 'jadwal_posyandu';

    public function getBulanIni(): array
    {
        return $this->query(
            "SELECT * FROM {$this->table}
             WHERE MONTH(tanggal) = MONTH(CURDATE())
               AND YEAR(tanggal) = YEAR(CURDATE())
             ORDER BY tanggal ASC, jam_mulai ASC"
        );
    }

    public function getMendatang(): array
    {
        return $this->query(
            "SELECT * FROM {$this->table}
             WHERE tanggal >= CURDATE()
               AND status IN ('dijadwalkan','berlangsung')
             ORDER BY tanggal ASC, jam_mulai ASC
             LIMIT 5"
        );
    }

    public function countBulanIni(): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE MONTH(tanggal) = MONTH(CURDATE())
               AND YEAR(tanggal) = YEAR(CURDATE())"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function paginate(int $page = 1, int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count();

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY tanggal DESC, jam_mulai DESC LIMIT ? OFFSET ?"
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
}
