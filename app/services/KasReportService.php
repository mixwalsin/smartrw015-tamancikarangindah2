<?php

declare(strict_types=1);

require_once APP_PATH . '/repositories/KasTransactionRepository.php';
require_once APP_PATH . '/repositories/KasBalanceRepository.php';

class KasReportService
{
    private KasTransactionRepository $transactions;
    private KasBalanceRepository $balances;

    public function __construct()
    {
        $this->transactions = new KasTransactionRepository();
        $this->balances = new KasBalanceRepository();
    }

    public function monthly(string $kasType, int $year, int $month, ?int $rtId = null): array
    {
        $items = $this->transactions->monthlyReport($kasType, $year, $month, $rtId);
        $byCategory = $this->transactions->expenseByCategory($kasType, $year, $month, $rtId);

        $income = 0.0;
        $expense = 0.0;
        foreach ($items as $item) {
            if ($item['transaction_type'] === 'pemasukan') {
                $income += (float) $item['amount'];
            } else {
                $expense += (float) $item['amount'];
            }
        }

        return [
            'items' => $items,
            'total_pemasukan' => $income,
            'total_pengeluaran' => $expense,
            'saldo' => $income - $expense,
            'expense_by_category' => $byCategory,
            'meta' => compact('kasType', 'year', 'month', 'rtId'),
        ];
    }

    public function yearly(string $kasType, int $year, ?int $rtId = null): array
    {
        $rows = $this->transactions->yearlyAggregation($kasType, $year, $rtId);
        $trend = $this->balances->trend($kasType, $rtId, $year);

        $map = [];
        foreach ($rows as $row) {
            $month = (int) $row['month'];
            $map[$month] = [
                'pemasukan' => (float) $row['pemasukan'],
                'pengeluaran' => (float) $row['pengeluaran'],
            ];
        }

        $series = [];
        $cumulativeIncome = 0.0;
        $cumulativeExpense = 0.0;
        for ($month = 1; $month <= 12; $month++) {
            $income = $map[$month]['pemasukan'] ?? 0.0;
            $expense = $map[$month]['pengeluaran'] ?? 0.0;
            $cumulativeIncome += $income;
            $cumulativeExpense += $expense;
            $series[] = [
                'month' => $month,
                'month_name' => $this->monthName($month),
                'pemasukan' => $income,
                'pengeluaran' => $expense,
                'saldo_bersih' => $income - $expense,
                'kumulatif_pemasukan' => $cumulativeIncome,
                'kumulatif_pengeluaran' => $cumulativeExpense,
            ];
        }

        return [
            'series' => $series,
            'balance_trend' => $trend,
            'meta' => compact('kasType', 'year', 'rtId'),
        ];
    }

    public function exportCsv(array $items): string
    {
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, ['Tanggal', 'Kas', 'Jenis', 'Kategori', 'Nominal', 'Deskripsi', 'Status']);

        foreach ($items as $item) {
            fputcsv($fp, [
                $item['date'],
                strtoupper((string) $item['kas_type']),
                $item['transaction_type'],
                $item['category_name'] ?? '-',
                (string) $item['amount'],
                $item['description'] ?? '',
                $item['status'],
            ]);
        }

        rewind($fp);
        return (string) stream_get_contents($fp);
    }

    public function reportHtml(string $title, array $items, array $summary): string
    {
        ob_start();
        ?>
        <html>
        <head>
            <meta charset="utf-8">
            <title><?= e($title) ?></title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 12px; }
                th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
                th { background: #f5f5f5; }
            </style>
        </head>
        <body>
            <h2><?= e($title) ?></h2>
            <p>Total Pemasukan: <?= rupiah($summary['total_pemasukan'] ?? 0) ?> | Total Pengeluaran: <?= rupiah($summary['total_pengeluaran'] ?? 0) ?> | Saldo: <?= rupiah($summary['saldo'] ?? 0) ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th><th>Kas</th><th>Jenis</th><th>Kategori</th><th>Nominal</th><th>Status</th><th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e((string) $item['date']) ?></td>
                        <td><?= e(strtoupper((string) $item['kas_type'])) ?></td>
                        <td><?= e((string) $item['transaction_type']) ?></td>
                        <td><?= e((string) ($item['category_name'] ?? '-')) ?></td>
                        <td><?= rupiah((float) $item['amount']) ?></td>
                        <td><?= e((string) $item['status']) ?></td>
                        <td><?= e((string) ($item['description'] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
        return (string) ob_get_clean();
    }

    private function monthName(int $month): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $months[$month] ?? '-';
    }
}
