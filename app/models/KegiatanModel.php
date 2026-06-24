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
        $stmt = $this->db->prepare('SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function mendatang(): array
    {
        return $this->query('SELECT * FROM kegiatan WHERE tanggal >= CURDATE() ORDER BY tanggal ASC');
    }
}
