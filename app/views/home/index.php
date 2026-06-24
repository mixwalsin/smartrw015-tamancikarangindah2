<div class="container py-4">
    <div class="row mb-5">
        <div class="col-12 text-center py-5">
            <h1 class="display-5 fw-bold text-primary mb-3"><i class="bi bi-building me-2"></i><?= e(APP_NAME) ?></h1>
            <p class="lead text-muted mb-4">Portal Digital Pelayanan Warga, Administrasi, Kas, Surat Online, Posyandu, Security, UMKM, Pengumuman, Laporan, dan Audit Log.</p>
            <?php if (!isLoggedIn()): ?>
                <a href="<?= url('auth/login') ?>" class="btn btn-primary btn-lg me-2">Login</a>
                <a href="<?= url('auth/register') ?>" class="btn btn-outline-primary btn-lg">Daftar</a>
            <?php else: ?>
                <a href="<?= url('dashboard') ?>" class="btn btn-primary btn-lg">Buka Dashboard</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ([
            ['icon' => 'bi-person-lock', 'judul' => 'Authentication & RBAC', 'deskripsi' => 'Login, registrasi, session, dan kontrol hak akses.'],
            ['icon' => 'bi-people-fill', 'judul' => 'Administrasi Penduduk', 'deskripsi' => 'Penduduk, KK, rumah, dan statistik wilayah.'],
            ['icon' => 'bi-envelope-paper-fill', 'judul' => 'Surat Online', 'deskripsi' => 'Pengajuan, persetujuan, dan cetak surat warga.'],
            ['icon' => 'bi-cash-stack', 'judul' => 'Kas RW / RT', 'deskripsi' => 'Pencatatan kas, saldo berjalan, dan laporan.'],
            ['icon' => 'bi-calendar-event-fill', 'judul' => 'Kegiatan & Pengumuman', 'deskripsi' => 'Agenda warga, broadcast, dan notifikasi.'],
            ['icon' => 'bi-file-earmark-bar-graph-fill', 'judul' => 'PDF / Excel Export', 'deskripsi' => 'Laporan siap cetak PDF dan ekspor CSV/Excel.'],
        ] as $item): ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="bi <?= e($item['icon']) ?> fs-1 text-primary mb-3 d-block"></i>
                        <h5 class="fw-semibold"><?= e($item['judul']) ?></h5>
                        <p class="text-muted small mb-0"><?= e($item['deskripsi']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
