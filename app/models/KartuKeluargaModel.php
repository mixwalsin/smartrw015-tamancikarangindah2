<?php

/**
 * KartuKeluargaModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KartuKeluargaModel extends Model
{
    protected string $table = 'kk';

    // ──────────────────────────────────────────
    // KK Queries
    // ──────────────────────────────────────────

    public function findByNoKk(string $noKk): array|false
    {
        return $this->findWhere('no_kk', $noKk);
    }

    /**
     * List KK with kepala keluarga name and anggota count
     */
    public function listWithDetail(int $page = 1, string $keyword = '', int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = '';
        $params = [];

        if ($keyword !== '') {
            $where  = "WHERE k.no_kk LIKE ? OR k.alamat LIKE ? OR w.nama LIKE ?";
            $params = ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"];
        }

        $countSql = "
            SELECT COUNT(DISTINCT k.id)
            FROM kk k
            LEFT JOIN keluarga kg ON kg.kk_id = k.id AND kg.hubungan = 'Kepala Keluarga'
            LEFT JOIN warga w ON w.id = kg.warga_id
            {$where}
        ";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "
            SELECT k.*,
                   w.nama AS kepala_keluarga,
                   w.nik  AS nik_kepala,
                   (SELECT COUNT(*) FROM keluarga kg2 WHERE kg2.kk_id = k.id) AS jumlah_anggota
            FROM kk k
            LEFT JOIN keluarga kg ON kg.kk_id = k.id AND kg.hubungan = 'Kepala Keluarga'
            LEFT JOIN warga w ON w.id = kg.warga_id
            {$where}
            ORDER BY k.rt_text ASC, k.no_kk ASC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $v) {
            $stmt->bindValue($i++, $v);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
            'keyword'      => $keyword,
        ];
    }

    /**
     * Find KK with kepala keluarga detail
     */
    public function findWithKepala(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT k.*,
                   w.nama AS kepala_keluarga,
                   w.nik  AS nik_kepala,
                   w.id   AS warga_kepala_id,
                   (SELECT COUNT(*) FROM keluarga kg2 WHERE kg2.kk_id = k.id) AS jumlah_anggota
            FROM kk k
            LEFT JOIN keluarga kg ON kg.kk_id = k.id AND kg.hubungan = 'Kepala Keluarga'
            LEFT JOIN warga w ON w.id = kg.warga_id
            WHERE k.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Check if a KK already has a Kepala Keluarga
     */
    public function hasKepalaKeluarga(int $kkId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM keluarga WHERE kk_id = ? AND hubungan = 'Kepala Keluarga'"
        );
        $stmt->execute([$kkId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Get all anggota of a KK (with hubungan)
     */
    public function getAnggota(int $kkId): array
    {
        return $this->query("
            SELECT w.*, kg.hubungan, kg.id AS keluarga_id
            FROM warga w
            JOIN keluarga kg ON kg.warga_id = w.id
            WHERE kg.kk_id = ?
            ORDER BY FIELD(kg.hubungan,'Kepala Keluarga','Istri','Anak','Menantu',
                           'Cucu','Orang Tua','Mertua','Famili Lain','Pembantu','Lainnya'),
                     w.tanggal_lahir ASC
        ", [$kkId]);
    }

    /**
     * Get all KK list (simple, for dropdown)
     */
    public function listForDropdown(): array
    {
        return $this->query("
            SELECT k.id, k.no_kk, k.alamat, k.rt_text,
                   COALESCE(w.nama,'—') AS kepala_keluarga
            FROM kk k
            LEFT JOIN keluarga kg ON kg.kk_id = k.id AND kg.hubungan = 'Kepala Keluarga'
            LEFT JOIN warga w ON w.id = kg.warga_id
            ORDER BY k.rt_text ASC, k.no_kk ASC
        ");
    }

    // ──────────────────────────────────────────
    // Keluarga (membership) operations
    // ──────────────────────────────────────────

    /**
     * Add warga to KK (insert into keluarga table)
     */
    public function addAnggota(int $kkId, int $wargaId, string $hubungan): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO keluarga (kk_id, warga_id, hubungan, created_at, updated_at)
             VALUES (?, ?, ?, NOW(), NOW())"
        );
        return $stmt->execute([$kkId, $wargaId, $hubungan]);
    }

    /**
     * Update hubungan anggota in keluarga table
     */
    public function updateHubungan(int $keluargaId, string $hubungan): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE keluarga SET hubungan = ?, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$hubungan, $keluargaId]);
    }

    /**
     * Remove anggota from KK (delete from keluarga)
     */
    public function removeAnggota(int $keluargaId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM keluarga WHERE id = ?");
        return $stmt->execute([$keluargaId]);
    }

    /**
     * Move warga to another KK: update kk_id in warga + keluarga
     */
    public function pindahAnggota(int $wargaId, int $newKkId, string $hubungan): bool
    {
        $this->db->beginTransaction();
        try {
            // Update warga.kk_id
            $s1 = $this->db->prepare("UPDATE warga SET kk_id = ?, updated_at = NOW() WHERE id = ?");
            $s1->execute([$newKkId, $wargaId]);

            // Update existing keluarga row
            $s2 = $this->db->prepare(
                "UPDATE keluarga SET kk_id = ?, hubungan = ?, updated_at = NOW() WHERE warga_id = ?"
            );
            $s2->execute([$newKkId, $hubungan, $wargaId]);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // ──────────────────────────────────────────
    // Riwayat operations
    // ──────────────────────────────────────────

    /**
     * Record a change to riwayat
     */
    public function logRiwayat(int $kkId, string $aksi, ?int $wargaId, string $keterangan, ?int $userId): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO kk_riwayat (kk_id, aksi, warga_id, keterangan, dilakukan_oleh, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$kkId, $aksi, $wargaId, $keterangan, $userId]);
    }

    /**
     * Get riwayat perubahan for a KK
     */
    public function getRiwayat(int $kkId): array
    {
        return $this->query("
            SELECT r.*, w.nama AS nama_warga, u.name AS nama_user
            FROM kk_riwayat r
            LEFT JOIN warga w ON w.id = r.warga_id
            LEFT JOIN users u ON u.id = r.dilakukan_oleh
            WHERE r.kk_id = ?
            ORDER BY r.created_at DESC
        ", [$kkId]);
    }
}
