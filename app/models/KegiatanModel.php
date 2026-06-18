<?php

/**
 * KegiatanModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class KegiatanModel extends Model
{
    protected string $table = 'kegiatan';

    public function terbaru(int $limit = 5): array
    {
        return $this->query(
            "SELECT * FROM {$this->table} ORDER BY tanggal DESC LIMIT ?",
            [$limit]
        );
    }

    public function mendatang(): array
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE tanggal >= CURDATE() ORDER BY tanggal ASC"
        );
    }
}
