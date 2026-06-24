<?php

/**
 * KeuanganModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KeuanganModel extends Model
{
    protected string $table = 'kas_rw';

    public function ringkasanBulanIni(): array
    {
        $rows = $this->query(
            'SELECT jenis, SUM(jumlah) as total
             FROM kas_rw
             WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
             GROUP BY jenis'
        );

        $result = ['pemasukan' => 0.0, 'pengeluaran' => 0.0];
        foreach ($rows as $row) {
            if ($row['jenis'] === 'pemasukan') {
                $result['pemasukan'] = (float) $row['total'];
            }
            if ($row['jenis'] === 'pengeluaran') {
                $result['pengeluaran'] = (float) $row['total'];
            }
        }

        $result['saldo'] = $result['pemasukan'] - $result['pengeluaran'];
        return $result;
    }

    public function saldoKasRw(): float
    {
        $row = $this->query('SELECT COALESCE(MAX(saldo_setelah), 0) AS saldo FROM kas_rw');
        return (float) ($row[0]['saldo'] ?? 0);
    }

    public function saldoKasRt(): float
    {
        $row = $this->query('SELECT COALESCE(SUM(saldo_per_rt.saldo), 0) AS saldo FROM (SELECT MAX(saldo_setelah) AS saldo FROM kas_rt GROUP BY rt_id) saldo_per_rt');
        return (float) ($row[0]['saldo'] ?? 0);
    }
}
