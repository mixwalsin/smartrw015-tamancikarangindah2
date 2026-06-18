<?php

/**
 * KartuKeluargaModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KartuKeluargaModel extends Model
{
    protected string $table = 'kartu_keluarga';

    public function findByNomorKk(string $nomorKk): array|false
    {
        return $this->findWhere('nomor_kk', $nomorKk);
    }

    public function paginateWithStats(int $page = 1, int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count();

        $sql = "SELECT kk.*, COALESCE(COUNT(ak.id), 0) AS jumlah_anggota_aktual
                FROM {$this->table} kk
                LEFT JOIN anggota_keluarga ak ON ak.kartu_keluarga_id = kk.id
                GROUP BY kk.id
                ORDER BY kk.created_at DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
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

    public function syncJumlahAnggota(int $id): void
    {
        $jumlah = hitungJumlahAnggotaKk($id);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET jumlah_anggota = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$jumlah, $id]);
    }
}
