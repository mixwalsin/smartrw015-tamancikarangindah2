<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KasCategoryModel extends Model
{
    protected string $table = 'kas_categories';

    public function findByType(string $kasType, string $transactionType): array
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE kas_type = ? AND transaction_type = ? ORDER BY name ASC",
            [$kasType, $transactionType]
        );
    }
}
