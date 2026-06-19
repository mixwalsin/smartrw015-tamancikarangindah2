<?php

declare(strict_types=1);

class StatistikController extends Controller
{
    private PendudukModel $pendudukModel;
    private GenericTableModel $rumahModel;
    private GenericTableModel $umkmModel;

    public function __construct()
    {
        $this->pendudukModel = new PendudukModel();
        $this->rumahModel = new GenericTableModel('rumah');
        $this->umkmModel = new GenericTableModel('umkm');
    }

    public function index(): void
    {
        $this->requireAuth();
        $this->view('statistik/index', [
            'title' => 'Statistik - ' . APP_NAME,
            'gender' => $this->pendudukModel->countByJenisKelamin(),
            'rtStats' => $this->pendudukModel->countByRt(),
            'totalRumah' => $this->rumahModel->count(),
            'totalUmkm' => $this->umkmModel->count(),
        ]);
    }
}
