<?php

/**
 * SuratModel - Master Jenis Surat
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class SuratModel extends Model
{
    protected string $table = 'surat_jenis';

    /**
     * Get all active surat types
     */
    public function getActive(): array
    {
        return $this->where('is_active', 1);
    }

    /**
     * Find by kode
     */
    public function findByKode(string $kode): array|false
    {
        return $this->findWhere('kode', $kode);
    }
}
