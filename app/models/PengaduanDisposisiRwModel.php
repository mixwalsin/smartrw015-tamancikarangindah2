<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PengaduanDisposisiRwModel extends Model
{
    protected string $table = 'pengaduan_disposisi_rw';

    public function findLatestByPengaduan(int $pengaduanId): array|false
    {
        $rows = $this->query(
            "SELECT * FROM {$this->table} WHERE pengaduan_id = ? ORDER BY created_at DESC LIMIT 1",
            [$pengaduanId]
        );

        return $rows[0] ?? false;
    }
}
