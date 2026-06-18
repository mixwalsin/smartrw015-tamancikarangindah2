<?php

declare(strict_types=1);

require_once APP_PATH . '/models/KasTransactionModel.php';

class KasTransactionRepository
{
    private KasTransactionModel $model;

    public function __construct()
    {
        $this->model = new KasTransactionModel();
    }

    public function paginate(array $filters, int $page = 1, int $perPage = PAGINATION_LIMIT): array
    {
        $where = ['1=1'];
        $bindings = [];

        if (!empty($filters['kas_type'])) {
            $where[] = 't.kas_type = ?';
            $bindings[] = $filters['kas_type'];
        }

        if (!empty($filters['transaction_type'])) {
            $where[] = 't.transaction_type = ?';
            $bindings[] = $filters['transaction_type'];
        }

        if (!empty($filters['category_id'])) {
            $where[] = 't.category_id = ?';
            $bindings[] = (int) $filters['category_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = 't.status = ?';
            $bindings[] = $filters['status'];
        }

        if (!empty($filters['rt_id'])) {
            $where[] = 't.rt_id = ?';
            $bindings[] = (int) $filters['rt_id'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = 't.date >= ?';
            $bindings[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = 't.date <= ?';
            $bindings[] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(t.description LIKE ? OR c.name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $bindings[] = $search;
            $bindings[] = $search;
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $countResult = $this->model->query(
            "SELECT COUNT(*) AS total
             FROM kas_transactions t
             LEFT JOIN kas_categories c ON c.id = t.category_id
             WHERE {$whereSql}",
            $bindings
        );

        $rows = $this->model->query(
            "SELECT t.*, c.name AS category_name, u.name AS created_by_name, rt.kode AS rt_kode
             FROM kas_transactions t
             LEFT JOIN kas_categories c ON c.id = t.category_id
             LEFT JOIN users u ON u.id = t.created_by
             LEFT JOIN rt ON rt.id = t.rt_id
             WHERE {$whereSql}
             ORDER BY t.date DESC, t.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $bindings
        );

        $total = (int) ($countResult[0]['total'] ?? 0);

        return [
            'data' => $rows,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    public function find(int $id): array|false
    {
        return $this->model->findWithRelations($id);
    }

    public function create(array $data): int
    {
        return (int) $this->model->insert($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function summaryThisMonth(): array
    {
        $rows = $this->model->query(
            "SELECT transaction_type, SUM(amount) AS total
             FROM kas_transactions
             WHERE status = 'approved'
               AND MONTH(date) = MONTH(CURDATE())
               AND YEAR(date) = YEAR(CURDATE())
             GROUP BY transaction_type"
        );

        $result = ['pemasukan' => 0.0, 'pengeluaran' => 0.0];
        foreach ($rows as $row) {
            $result[$row['transaction_type']] = (float) $row['total'];
        }

        return $result;
    }

    public function recent(int $limit = 5): array
    {
        return $this->model->query(
            "SELECT t.*, c.name AS category_name
             FROM kas_transactions t
             LEFT JOIN kas_categories c ON c.id = t.category_id
             ORDER BY t.date DESC, t.id DESC
             LIMIT {$limit}"
        );
    }

    public function monthlyReport(string $kasType, int $year, int $month, ?int $rtId = null): array
    {
        $sql = "SELECT t.*, c.name AS category_name
                FROM kas_transactions t
                LEFT JOIN kas_categories c ON c.id = t.category_id
                WHERE t.status = 'approved'
                  AND t.kas_type = ?
                  AND YEAR(t.date) = ?
                  AND MONTH(t.date) = ?";
        $bindings = [$kasType, $year, $month];

        if ($rtId !== null) {
            $sql .= ' AND t.rt_id = ?';
            $bindings[] = $rtId;
        }

        $sql .= ' ORDER BY t.date ASC, t.id ASC';

        return $this->model->query($sql, $bindings);
    }

    public function yearlyAggregation(string $kasType, int $year, ?int $rtId = null): array
    {
        $sql = "SELECT MONTH(date) AS month,
                       SUM(CASE WHEN transaction_type = 'pemasukan' THEN amount ELSE 0 END) AS pemasukan,
                       SUM(CASE WHEN transaction_type = 'pengeluaran' THEN amount ELSE 0 END) AS pengeluaran
                FROM kas_transactions
                WHERE status = 'approved' AND kas_type = ? AND YEAR(date) = ?";
        $bindings = [$kasType, $year];

        if ($rtId !== null) {
            $sql .= ' AND rt_id = ?';
            $bindings[] = $rtId;
        }

        $sql .= ' GROUP BY MONTH(date) ORDER BY MONTH(date)';

        return $this->model->query($sql, $bindings);
    }

    public function expenseByCategory(string $kasType, int $year, ?int $month = null, ?int $rtId = null): array
    {
        $sql = "SELECT c.name, SUM(t.amount) AS total
                FROM kas_transactions t
                INNER JOIN kas_categories c ON c.id = t.category_id
                WHERE t.status = 'approved'
                  AND t.kas_type = ?
                  AND t.transaction_type = 'pengeluaran'
                  AND YEAR(t.date) = ?";
        $bindings = [$kasType, $year];

        if ($month !== null) {
            $sql .= ' AND MONTH(t.date) = ?';
            $bindings[] = $month;
        }

        if ($rtId !== null) {
            $sql .= ' AND t.rt_id = ?';
            $bindings[] = $rtId;
        }

        $sql .= ' GROUP BY c.name ORDER BY total DESC';

        return $this->model->query($sql, $bindings);
    }
}
