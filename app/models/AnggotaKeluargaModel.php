<?php

/**
 * AnggotaKeluargaModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class AnggotaKeluargaModel extends Model
{
    protected string $table = 'anggota_keluarga';

    public function getByKartuKeluarga(int $kartuKeluargaId): array
    {
        $sql = "SELECT ak.*, w.nik, w.nama
                FROM {$this->table} ak
                LEFT JOIN warga w ON w.id = ak.warga_id
                WHERE ak.kartu_keluarga_id = ?
                ORDER BY ak.id ASC";

        return $this->query($sql, [$kartuKeluargaId]);
    }

    public function existsWargaInKartuKeluarga(int $kartuKeluargaId, int $wargaId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE kartu_keluarga_id = ? AND warga_id = ?"
        );
        $stmt->execute([$kartuKeluargaId, $wargaId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
