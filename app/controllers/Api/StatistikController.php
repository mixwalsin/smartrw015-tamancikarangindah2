<?php

declare(strict_types=1);

namespace Api;

class StatistikController extends \Controller
{
    public function index(): void
    {
        $model = new \PendudukModel();
        $rumah = new \GenericTableModel('rumah');
        $this->json([
            'penduduk_per_rt' => $model->countByRt(),
            'jenis_kelamin' => $model->countByJenisKelamin(),
            'total_rumah' => $rumah->count(),
        ]);
    }
}
