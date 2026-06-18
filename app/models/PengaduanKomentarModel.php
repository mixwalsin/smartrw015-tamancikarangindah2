<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PengaduanKomentarModel extends Model
{
    protected string $table = 'pengaduan_komentar';

    public function getByPengaduan(int $pengaduanId): array
    {
        return $this->query(
            "SELECT pk.*, u.name AS nama_user
             FROM {$this->table} pk
             LEFT JOIN users u ON u.id = pk.user_id
             WHERE pk.pengaduan_id = ?
             ORDER BY pk.created_at ASC",
            [$pengaduanId]
        );
    }
}
