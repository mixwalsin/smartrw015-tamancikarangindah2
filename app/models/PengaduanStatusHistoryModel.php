<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PengaduanStatusHistoryModel extends Model
{
    protected string $table = 'pengaduan_status_history';

    public function getByPengaduan(int $pengaduanId): array
    {
        return $this->query(
            "SELECT h.*, u.name AS changed_by_name
             FROM {$this->table} h
             LEFT JOIN users u ON u.id = h.changed_by
             WHERE h.pengaduan_id = ?
             ORDER BY h.changed_at ASC, h.id ASC",
            [$pengaduanId]
        );
    }
}
