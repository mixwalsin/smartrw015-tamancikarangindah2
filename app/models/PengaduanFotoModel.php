<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PengaduanFotoModel extends Model
{
    protected string $table = 'pengaduan_foto';

    public function getByPengaduan(int $pengaduanId): array
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE pengaduan_id = ? ORDER BY created_at ASC",
            [$pengaduanId]
        );
    }
}
