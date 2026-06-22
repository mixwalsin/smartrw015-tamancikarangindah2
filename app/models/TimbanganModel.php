<?php

/**
 * TimbanganModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class TimbanganModel extends Model
{
    protected string $table = 'timbangan';

    /**
     * Ambil semua data timbangan beserta nama balita
     */
    public function allWithBalita(int $page = 1, int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} t JOIN balita b ON t.balita_id = b.id"
        );
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT t.*, b.nama AS nama_balita
             FROM {$this->table} t
             JOIN balita b ON t.balita_id = b.id
             ORDER BY t.tanggal_timbang DESC
             LIMIT ? OFFSET ?"
        );
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

    public function getByBalita(int $balitaId): array
    {
        return $this->query(
            "SELECT * FROM {$this->table}
             WHERE balita_id = ?
             ORDER BY tanggal_timbang ASC",
            [$balitaId]
        );
    }

    public function countGiziKurang(): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT balita_id) FROM {$this->table}
             WHERE status_gizi IN ('gizi_kurang','gizi_buruk')
               AND tanggal_timbang = (
                   SELECT MAX(t2.tanggal_timbang)
                   FROM timbangan t2
                   WHERE t2.balita_id = timbangan.balita_id
               )"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Hitung status gizi berdasarkan berat badan (BB/U) menggunakan Z-score WHO.
     *
     * Konstanta ESTIMATED_SD_PERCENTAGE (12% dari median) adalah pendekatan sederhana.
     * Untuk produksi, gunakan tabel SD penuh dari WHO child growth standards.
     *
     * @param float  $beratBadan   Berat badan dalam kg
     * @param int    $usiaBulan    Usia dalam bulan
     * @param string $jenisKelamin 'L' (laki-laki) atau 'P' (perempuan)
     */
    private const ESTIMATED_SD_PERCENTAGE = 0.12;

    public static function hitungStatusGizi(float $beratBadan, int $usiaBulan, string $jenisKelamin): string
    {
        // Median BB/U WHO (nilai perkiraan sederhana untuk skrining awal)
        // Untuk produksi, gunakan tabel lengkap WHO Child Growth Standards
        $medianBBU = [
            // [laki-laki_median, perempuan_median]
            0  => [3.3,  3.2],
            1  => [4.5,  4.2],
            2  => [5.6,  5.1],
            3  => [6.4,  5.8],
            4  => [7.0,  6.4],
            5  => [7.5,  6.9],
            6  => [7.9,  7.3],
            7  => [8.3,  7.6],
            8  => [8.6,  7.9],
            9  => [8.9,  8.2],
            10 => [9.2,  8.5],
            11 => [9.4,  8.7],
            12 => [9.6,  8.9],
            18 => [10.9, 10.2],
            24 => [12.2, 11.5],
            36 => [14.3, 13.9],
            48 => [16.3, 15.8],
            60 => [18.3, 17.7],
        ];

        // Cari referensi terdekat
        $keys = array_keys($medianBBU);
        $closest = $keys[0];
        foreach ($keys as $k) {
            if (abs($k - $usiaBulan) < abs($closest - $usiaBulan)) {
                $closest = $k;
            }
        }

        $colIdx = ($jenisKelamin === 'L') ? 0 : 1;
        $median = $medianBBU[$closest][$colIdx];
        $sd     = $median * self::ESTIMATED_SD_PERCENTAGE;

        $zscore = ($beratBadan - $median) / $sd;

        if ($zscore < -3) {
            return 'gizi_buruk';
        }
        if ($zscore < -2) {
            return 'gizi_kurang';
        }
        if ($zscore > 2) {
            return 'lebih';
        }
        return 'gizi_baik';
    }
}
