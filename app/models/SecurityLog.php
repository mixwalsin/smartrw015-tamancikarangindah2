<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class SecurityLog extends Model
{
    protected string $table = 'security_log';

    public function add(string $tipe, ?int $userId, ?int $objectId, string $deskripsi): void
    {
        $this->insert([
            'tipe_aktivitas' => $tipe,
            'user_id' => $userId,
            'object_id' => $objectId,
            'deskripsi' => $deskripsi,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    public function latest(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
