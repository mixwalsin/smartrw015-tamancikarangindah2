<?php

declare(strict_types=1);

require_once APP_PATH . '/models/KasBalanceModel.php';
require_once APP_PATH . '/models/KasBalanceHistoryModel.php';

class KasBalanceRepository
{
    private KasBalanceModel $balanceModel;
    private KasBalanceHistoryModel $historyModel;

    public function __construct()
    {
        $this->balanceModel = new KasBalanceModel();
        $this->historyModel = new KasBalanceHistoryModel();
    }

    public function getBalance(string $kasType, ?int $rtId = null): float
    {
        $row = $this->balanceModel->findByKasType($kasType, $rtId);
        return (float) ($row['balance'] ?? 0);
    }

    public function listBalances(): array
    {
        return $this->balanceModel->query(
            "SELECT b.*, rt.kode AS rt_kode
             FROM kas_balance b
             LEFT JOIN rt ON rt.id = b.rt_id
             ORDER BY b.kas_type ASC, rt.kode ASC"
        );
    }

    public function updateBalance(string $kasType, ?int $rtId, float $balance, ?int $transactionId = null): void
    {
        $existing = $this->balanceModel->findByKasType($kasType, $rtId);
        $payload = [
            'kas_type' => $kasType,
            'rt_id' => $rtId,
            'balance' => $balance,
            'last_updated' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->balanceModel->update((int) $existing['id'], $payload);
        } else {
            $this->balanceModel->insert($payload);
        }

        $this->historyModel->insert([
            'kas_type' => $kasType,
            'rt_id' => $rtId,
            'balance' => $balance,
            'transaction_id' => $transactionId,
        ]);
    }

    public function trend(string $kasType, ?int $rtId, int $year): array
    {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS period, MAX(balance) AS balance
                FROM kas_balance_history
                WHERE kas_type = ? AND YEAR(created_at) = ?";
        $bindings = [$kasType, $year];

        if ($rtId === null) {
            $sql .= ' AND rt_id IS NULL';
        } else {
            $sql .= ' AND rt_id = ?';
            $bindings[] = $rtId;
        }

        $sql .= ' GROUP BY period ORDER BY period ASC';

        return $this->historyModel->query($sql, $bindings);
    }
}
