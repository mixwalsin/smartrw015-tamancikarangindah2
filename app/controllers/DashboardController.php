<?php

/**
 * DashboardController
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/PendudukModel.php';
require_once APP_PATH . '/models/PengaduanModel.php';
require_once APP_PATH . '/models/KegiatanModel.php';
require_once APP_PATH . '/models/KeuanganModel.php';

class DashboardController extends Controller
{
    private PendudukModel  $pendudukModel;
    private PengaduanModel $pengaduanModel;
    private KegiatanModel  $kegiatanModel;
    private KeuanganModel  $keuanganModel;

    public function __construct()
    {
        $this->pendudukModel  = new PendudukModel();
        $this->pengaduanModel = new PengaduanModel();
        $this->kegiatanModel  = new KegiatanModel();
        $this->keuanganModel  = new KeuanganModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $this->view('dashboard/index', [
            'title'            => 'Dashboard - ' . APP_NAME,
            'totalPenduduk'    => $this->pendudukModel->count(),
            'totalPengaduan'   => $this->pengaduanModel->count(),
            'kegiatanTerbaru'  => $this->kegiatanModel->terbaru(5),
            'ringkasanKeuangan'=> $this->keuanganModel->ringkasanBulanIni(),
        ]);
    }

    public function rw(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw');
        $this->view('dashboard/rw', [
            'title' => 'Dashboard RW - ' . APP_NAME,
        ]);
    }

    public function rt(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $this->view('dashboard/rt', [
            'title' => 'Dashboard RT - ' . APP_NAME,
        ]);
    }
}
