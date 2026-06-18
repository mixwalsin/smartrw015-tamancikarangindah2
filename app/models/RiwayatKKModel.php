<?php

/**
 * RiwayatKKModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class RiwayatKKModel extends Model
{
    protected string $table = 'riwayat_kk';

    public function log(
        int $kartuKeluargaId,
        string $aksi,
        ?string $keterangan = null,
        ?string $dataSebelum = null,
        ?string $dataSesudah = null
    ): void {
        $this->insert([
            'kartu_keluarga_id' => $kartuKeluargaId,
            'aksi'              => $aksi,
            'keterangan'        => $keterangan,
            'data_sebelum'      => $dataSebelum,
            'data_sesudah'      => $dataSesudah,
            'diubah_oleh'       => authUser()['name'] ?? 'system',
        ]);
    }

    public function getByKartuKeluarga(int $kartuKeluargaId): array
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE kartu_keluarga_id = ? ORDER BY created_at DESC, id DESC",
            [$kartuKeluargaId]
        );
    }
}
