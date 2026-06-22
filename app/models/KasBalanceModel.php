<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KasBalanceModel extends Model
{
    protected string $table = 'kas_balance';

    public function findByKasType(string $kasType, ?int $rtId = null): array|false
    {
        if ($rtId === null) {
            return $this->query("SELECT * FROM {$this->table} WHERE kas_type = ? AND rt_id IS NULL LIMIT 1", [$kasType])[0] ?? false;
        }

        return $this->query("SELECT * FROM {$this->table} WHERE kas_type = ? AND rt_id = ? LIMIT 1", [$kasType, $rtId])[0] ?? false;
    }
}
