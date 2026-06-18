<!-- Dashboard Utama -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
        <span class="text-muted small"><?= formatDate(date('Y-m-d'), 'd F Y') ?></span>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-people-fill fs-2"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= number_format($totalPenduduk ?? 0) ?></div>
                        <div class="small opacity-75">Total Penduduk</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-megaphone-fill fs-2"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= number_format($totalPengaduan ?? 0) ?></div>
                        <div class="small opacity-75">Total Pengaduan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-arrow-up-circle-fill fs-2"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= rupiah($ringkasanKeuangan['pemasukan'] ?? 0) ?></div>
                        <div class="small opacity-75">Pemasukan Bulan Ini</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-arrow-down-circle-fill fs-2"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= rupiah($ringkasanKeuangan['pengeluaran'] ?? 0) ?></div>
                        <div class="small opacity-75">Pengeluaran Bulan Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kegiatan Terbaru -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom fw-semibold">
                    <i class="bi bi-calendar-event text-primary me-2"></i>Kegiatan Terbaru
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($kegiatanTerbaru)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($kegiatanTerbaru as $kegiatan): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold"><?= e($kegiatan['judul']) ?></div>
                                        <small class="text-muted"><?= formatDate($kegiatan['tanggal']) ?></small>
                                    </div>
                                    <a href="<?= url('kegiatan/show/' . $kegiatan['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="p-3 text-muted text-center">Belum ada kegiatan.</div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <a href="<?= url('kegiatan') ?>" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom fw-semibold">
                    <i class="bi bi-cash-stack text-success me-2"></i>Ringkasan Keuangan Bulan Ini
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Pemasukan</td>
                            <td class="fw-semibold text-success"><?= rupiah($ringkasanKeuangan['pemasukan'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pengeluaran</td>
                            <td class="fw-semibold text-danger"><?= rupiah($ringkasanKeuangan['pengeluaran'] ?? 0) ?></td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold">Saldo</td>
                            <td class="fw-bold text-primary"><?= rupiah($ringkasanKeuangan['saldo'] ?? 0) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <a href="<?= url('keuangan') ?>" class="btn btn-sm btn-success">Detail Keuangan</a>
                </div>
            </div>
        </div>
    </div>

</div>
