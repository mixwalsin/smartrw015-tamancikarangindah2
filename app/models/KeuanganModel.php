<?php

/**
 * KeuanganModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KeuanganModel extends Model
{
    protected string $table = 'kas_transactions';

    public function ringkasanBulanIni(): array
    {
        $rows = $this->query(
            "SELECT transaction_type, SUM(amount) as total
             FROM {$this->table}
             WHERE status = 'approved'
               AND MONTH(date) = MONTH(CURDATE())
               AND YEAR(date) = YEAR(CURDATE())
             GROUP BY transaction_type"
        );

        $result = ['pemasukan' => 0, 'pengeluaran' => 0];
        foreach ($rows as $row) {
            if ($row['transaction_type'] === 'pemasukan') {
                $result['pemasukan'] = (float) $row['total'];
            } elseif ($row['transaction_type'] === 'pengeluaran') {
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
                SUM(CASE WHEN transaction_type = 'pemasukan' THEN amount ELSE 0 END) -
                SUM(CASE WHEN transaction_type = 'pengeluaran' THEN amount ELSE 0 END) AS saldo
             FROM {$this->table}
             WHERE status = 'approved'"
        );
        return (float) ($row[0]['saldo'] ?? 0);
    }
}
