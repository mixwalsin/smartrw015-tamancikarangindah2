<?php

/**
 * WargaModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class WargaModel extends Model
{
    protected string $table = 'warga';

    public function findByNik(string $nik): array|false
    {
        return $this->findWhere('nik', $nik);
    }

    public function getByKk(int $kkId): array
    {
        return $this->where('kk_id', $kkId);
    }

    /**
     * Search warga not yet assigned to any KK (for manual assign)
     */
    public function searchAvailable(string $keyword): array
    {
        return $this->query(
            "SELECT w.* FROM warga w
             LEFT JOIN keluarga kg ON kg.warga_id = w.id
             WHERE kg.id IS NULL
               AND (w.nik LIKE ? OR w.nama LIKE ?)
             LIMIT 20",
            ["%{$keyword}%", "%{$keyword}%"]
        );
    }

    /**
     * Get warga with their current KK info
     */
    public function findWithKk(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT w.*, k.no_kk, k.alamat AS alamat_kk, k.rt_text,
                   kg.hubungan, kg.id AS keluarga_id, kg.kk_id
            FROM warga w
            LEFT JOIN keluarga kg ON kg.warga_id = w.id
            LEFT JOIN kk k ON k.id = kg.kk_id
            WHERE w.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
