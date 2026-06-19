<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class NotificationModel extends Model
{
    protected string $table = 'notifikasi';

    public function unreadCountForUser(?int $userId): int
    {
        if ($userId === null) {
            return 0;
        }

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notifikasi WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function listForUser(?int $userId): array
    {
        if ($userId === null) {
            return [];
        }

        return $this->query(
            'SELECT * FROM notifikasi WHERE user_id = ? OR user_id IS NULL ORDER BY created_at DESC LIMIT 20',
            [$userId]
        );
    }
}
