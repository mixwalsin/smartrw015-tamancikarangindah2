<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PengaduanDisposisiRtModel extends Model
{
    protected string $table = 'pengaduan_disposisi_rt';

    public function findLatestByPengaduan(int $pengaduanId): array|false
    {
        $rows = $this->query(
            "SELECT prt.*, u.name AS petugas_nama
             FROM {$this->table} prt
             LEFT JOIN users u ON u.id = prt.petugas_id
             WHERE prt.pengaduan_id = ?
             ORDER BY prt.created_at DESC
             LIMIT 1",
            [$pengaduanId]
        );

        return $rows[0] ?? false;
    }
}
