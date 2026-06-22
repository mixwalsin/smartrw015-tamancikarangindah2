<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class Kendaraan extends Model
{
    protected string $table = 'kendaraan';

    public function parkedNow(): array
    {
        return $this->query("SELECT * FROM {$this->table} WHERE status = 'parkir' ORDER BY jam_masuk DESC");
    }

    public function checkout(int $id): bool
    {
        return $this->execute(
            "UPDATE {$this->table}
             SET jam_keluar = NOW(),
                 durasi_parkir = TIMESTAMPDIFF(MINUTE, TIMESTAMP(tanggal_masuk, jam_masuk), NOW()),
                 status = 'keluar',
                 updated_at = NOW()
             WHERE id = ? AND jam_keluar IS NULL",
            [$id]
        );
    }

    public function countToday(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE tanggal_masuk = CURDATE()");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
