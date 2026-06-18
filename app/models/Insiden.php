<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class Insiden extends Model
{
    protected string $table = 'insiden';

    public function pending(): array
    {
        return $this->query("SELECT * FROM {$this->table} WHERE status IN ('baru','diproses') ORDER BY tanggal_insiden DESC, jam_insiden DESC");
    }

    public function countPending(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE status IN ('baru','diproses')");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function updateStatus(int $id, string $status, ?string $petugas, ?string $catatan): bool
    {
        return $this->execute(
            "UPDATE {$this->table} SET status = ?, petugas_penangani = ?, catatan_penanganan = ?, updated_at = NOW() WHERE id = ?",
            [$status, $petugas, $catatan, $id]
        );
    }

    public function summaryByType(int $days = 30): array
    {
        return $this->query(
            "SELECT tipe_insiden, COUNT(*) AS total
             FROM {$this->table}
             WHERE tanggal_insiden >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY tipe_insiden
             ORDER BY total DESC",
            [$days]
        );
    }
}
