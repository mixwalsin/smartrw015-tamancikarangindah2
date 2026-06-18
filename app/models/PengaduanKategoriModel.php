<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PengaduanKategoriModel extends Model
{
    protected string $table = 'pengaduan_kategori';

    public function activeCategories(): array
    {
        return $this->all('name');
    }
}
