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

    public function getWithUser(): array
    {
        return $this->query(
            "SELECT p.*, u.name as nama_pelapor
             FROM {$this->table} p
             LEFT JOIN users u ON p.user_id = u.id
             ORDER BY p.created_at DESC"
        );
    }

    public function getByStatus(string $status): array
    {
        return $this->where('status', $status);
    }

    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId);
    }

    public function updateStatus(int $id, string $status, string $catatan = ''): bool
    {
        return $this->execute(
            "UPDATE {$this->table} SET status = ?, catatan_admin = ?, updated_at = ? WHERE id = ?",
            [$status, $catatan, date('Y-m-d H:i:s'), $id]
        );
    }
}
