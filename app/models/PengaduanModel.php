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

    public function count(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM pengaduan');
        return (int) $stmt->fetchColumn();
    }

    public function getWithUser(): array
    {
        return $this->query(
            'SELECT p.*, w.nama as nama_pelapor
             FROM pengaduan p
             LEFT JOIN warga w ON p.warga_id = w.id
             ORDER BY p.created_at DESC'
        );
    }

    public function getByStatus(string $status): array
    {
        return $this->where('status', $status);
    }

    public function getByWarga(int $wargaId): array
    {
        return $this->where('warga_id', $wargaId);
    }

    public function updateStatus(int $id, string $status, string $catatan = '', ?int $ditanganiOleh = null): bool
    {
        return $this->execute(
            'UPDATE pengaduan SET status = ?, catatan_admin = ?, ditangani_oleh = ?, ditangani_at = ?, updated_at = ? WHERE id = ?',
            [$status, $catatan, $ditanganiOleh, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]
        );
    }
}
