<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class NotifikasiModel extends Model
{
    protected string $table = 'notifikasi';

    public function latestForUser(?int $userId, int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id IS NULL";
        $bindings = [];

        if ($userId !== null) {
            $sql .= " OR user_id = ?";
            $bindings[] = $userId;
        }

        $sql .= " ORDER BY created_at DESC LIMIT " . max(1, $limit);

        return $this->query($sql, $bindings);
    }
}
