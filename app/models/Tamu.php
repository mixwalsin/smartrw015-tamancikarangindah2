<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class Tamu extends Model
{
    protected string $table = 'tamu';

    public function activeToday(?string $rt = null, ?string $rw = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE tanggal_kunjungan = CURDATE()";
        $params = [];

        if ($rt !== null && $rt !== '') {
            $sql .= ' AND rt = ?';
            $params[] = $rt;
        }

        if ($rw !== null && $rw !== '') {
            $sql .= ' AND rw = ?';
            $params[] = $rw;
        }

        $sql .= ' ORDER BY jam_masuk DESC';
        return $this->query($sql, $params);
    }

    public function checkout(int $id): bool
    {
        return $this->execute(
            "UPDATE {$this->table}
             SET jam_keluar = NOW(),
                 durasi_kunjungan = TIMESTAMPDIFF(MINUTE, TIMESTAMP(tanggal_kunjungan, jam_masuk), NOW()),
                 updated_at = NOW()
             WHERE id = ? AND jam_keluar IS NULL",
            [$id]
        );
    }

    public function countToday(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE tanggal_kunjungan = CURDATE()");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function pendingCheckout(): array
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE tanggal_kunjungan = CURDATE() AND jam_keluar IS NULL ORDER BY jam_masuk ASC"
        );
    }
}
