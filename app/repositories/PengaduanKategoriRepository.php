<?php

declare(strict_types=1);

require_once APP_PATH . '/models/PengaduanKategoriModel.php';

class PengaduanKategoriRepository
{
    private PengaduanKategoriModel $model;

    public function __construct()
    {
        $this->model = new PengaduanKategoriModel();
    }

    public function all(): array
    {
        return $this->model->activeCategories();
    }

    public function find(int $id): array|false
    {
        return $this->model->find($id);
    }
}
