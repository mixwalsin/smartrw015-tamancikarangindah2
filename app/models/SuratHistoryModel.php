<?php

/**
 * SuratHistoryModel - Audit Trail Surat
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class SuratHistoryModel extends Model
{
    protected string $table = 'surat_history';

    /**
     * Get history for a specific pengajuan (with user info)
     */
    public function getByPengajuan(int $pengajuanId): array
    {
        return $this->query(
            "SELECT sh.*, u.name AS user_nama, u.username
             FROM {$this->table} sh
             JOIN users u ON u.id = sh.dilakukan_oleh
             WHERE sh.pengajuan_id = ?
             ORDER BY sh.created_at ASC",
            [$pengajuanId]
        );
    }

    // Override insert to not add updated_at
    public function insert(array $data): string|false
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }
}
