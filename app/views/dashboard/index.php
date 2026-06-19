<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
        <span class="text-muted small"><?= formatDate(date('Y-m-d'), 'd F Y') ?></span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm bg-primary text-white"><div class="card-body"><div class="fs-4 fw-bold"><?= number_format($totalPenduduk ?? 0) ?></div><div class="small opacity-75">Total Penduduk</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm bg-warning text-dark"><div class="card-body"><div class="fs-4 fw-bold"><?= number_format($totalPengaduan ?? 0) ?></div><div class="small opacity-75">Total Pengaduan</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm bg-success text-white"><div class="card-body"><div class="fs-4 fw-bold"><?= rupiah($ringkasanKeuangan['pemasukan'] ?? 0) ?></div><div class="small opacity-75">Pemasukan Bulan Ini</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm bg-danger text-white"><div class="card-body"><div class="fs-4 fw-bold"><?= rupiah($ringkasanKeuangan['pengeluaran'] ?? 0) ?></div><div class="small opacity-75">Pengeluaran Bulan Ini</div></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-grid me-2 text-primary"></i>Layanan Utama</div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ([
                            ['label' => 'Penduduk', 'url' => 'penduduk', 'icon' => 'bi-people'],
                            ['label' => 'Kartu Keluarga', 'url' => 'kk', 'icon' => 'bi-card-list'],
                            ['label' => 'Rumah', 'url' => 'rumah', 'icon' => 'bi-house'],
                            ['label' => 'Surat', 'url' => 'surat', 'icon' => 'bi-envelope'],
                            ['label' => 'Kas RW / RT', 'url' => 'keuangan', 'icon' => 'bi-cash-stack'],
                            ['label' => 'Pengaduan', 'url' => 'pengaduan', 'icon' => 'bi-megaphone'],
                            ['label' => 'Kegiatan', 'url' => 'kegiatan', 'icon' => 'bi-calendar-event'],
                            ['label' => 'UMKM', 'url' => 'umkm', 'icon' => 'bi-shop'],
                            ['label' => 'Posyandu', 'url' => 'posyandu', 'icon' => 'bi-heart-pulse'],
                            ['label' => 'Security', 'url' => 'keamanan', 'icon' => 'bi-shield-check'],
                            ['label' => 'Pengumuman', 'url' => 'pengumuman', 'icon' => 'bi-megaphone'],
                            ['label' => 'Laporan', 'url' => 'laporan', 'icon' => 'bi-file-earmark-bar-graph'],
                        ] as $item): ?>
                            <div class="col-md-4">
                                <a href="<?= url($item['url']) ?>" class="text-decoration-none">
                                    <div class="border rounded p-3 h-100 text-center bg-light-subtle">
                                        <i class="bi <?= e($item['icon']) ?> fs-3 text-primary d-block mb-2"></i>
                                        <div class="fw-semibold small"><?= e($item['label']) ?></div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-calendar-event me-2 text-primary"></i>Kegiatan Terbaru</div>
                <ul class="list-group list-group-flush">
                    <?php foreach (($kegiatanTerbaru ?? []) as $kegiatan): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold"><?= e($kegiatan['judul']) ?></div>
                                <small class="text-muted"><?= formatDate((string) $kegiatan['tanggal']) ?></small>
                            </div>
                            <a href="<?= url('kegiatan/show/' . $kegiatan['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-bell me-2 text-warning"></i>Notifikasi Terbaru</div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($notifikasi)): ?>
                        <?php foreach ($notifikasi as $notif): ?>
                            <li class="list-group-item">
                                <div class="fw-semibold"><?= e($notif['judul']) ?></div>
                                <small class="text-muted d-block"><?= e(truncate($notif['pesan'], 80)) ?></small>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">Belum ada notifikasi.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
