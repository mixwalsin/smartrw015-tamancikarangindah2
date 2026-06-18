<?php

declare(strict_types=1);

require_once APP_PATH . '/models/KasCategoryModel.php';

class KasCategoryRepository
{
    private KasCategoryModel $model;

    public function __construct()
    {
        $this->model = new KasCategoryModel();
    }

    public function all(?string $kasType = null, ?string $transactionType = null): array
    {
        if ($kasType !== null && $transactionType !== null) {
            return $this->model->findByType($kasType, $transactionType);
        }

        return $this->model->all('name', 'ASC');
    }

    public function find(int $id): array|false
    {
        return $this->model->find($id);
    }

    public function create(array $data): int
    {
        return (int) $this->model->insert($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
}
