<?php

declare(strict_types=1);

namespace Api;

class PengaduanController extends \Controller
{
    public function index(): void
    {
        $model = new \PengaduanModel();
        $this->json($model->getWithUser());
    }
}
