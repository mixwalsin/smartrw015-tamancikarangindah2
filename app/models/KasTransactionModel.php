<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KasTransactionModel extends Model
{
    protected string $table = 'kas_transactions';

    public function findWithRelations(int $id): array|false
    {
        $rows = $this->query(
            "SELECT t.*, c.name AS category_name, u.name AS created_by_name
             FROM {$this->table} t
             LEFT JOIN kas_categories c ON c.id = t.category_id
             LEFT JOIN users u ON u.id = t.created_by
             WHERE t.id = ? LIMIT 1",
            [$id]
        );

        return $rows[0] ?? false;
    }
}
