<?php

/**
 * KeuanganModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KeuanganModel extends Model
{
    protected string $table = 'keuangan';

    public function ringkasanBulanIni(): array
    {
        $rows = $this->query(
            "SELECT jenis, SUM(jumlah) as total
             FROM {$this->table}
             WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
             GROUP BY jenis"
        );

        $result = ['pemasukan' => 0, 'pengeluaran' => 0];
        foreach ($rows as $row) {
            if ($row['jenis'] === 'pemasukan') {
                $result['pemasukan'] = (float) $row['total'];
            } elseif ($row['jenis'] === 'pengeluaran') {
                $result['pengeluaran'] = (float) $row['total'];
            }
        }
        $result['saldo'] = $result['pemasukan'] - $result['pengeluaran'];
        return $result;
    }

    public function saldoTotal(): float
    {
        $row = $this->query(
            "SELECT
                SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) -
                SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) AS saldo
             FROM {$this->table}"
        );
        return (float) ($row[0]['saldo'] ?? 0);
    }
}
