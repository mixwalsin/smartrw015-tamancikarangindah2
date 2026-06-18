<!-- Beranda Smart RW015 -->
<div class="container">

    <!-- Hero -->
    <div class="row mb-5">
        <div class="col-12 text-center py-5">
            <h1 class="display-5 fw-bold text-primary mb-3">
                <i class="bi bi-building me-2"></i><?= e(APP_NAME) ?>
            </h1>
            <p class="lead text-muted mb-4">Portal Digital Pelayanan Warga RW015 Taman Cikarang Indah 2</p>
            <?php if (!isLoggedIn()): ?>
                <a href="<?= url('auth/login') ?>" class="btn btn-primary btn-lg me-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
                <a href="<?= url('auth/register') ?>" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-person-plus me-1"></i>Daftar
                </a>
            <?php else: ?>
                <a href="<?= url('dashboard') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-speedometer2 me-1"></i>Buka Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Fitur Layanan -->
    <div class="row g-4 mb-5">
        <div class="col-12">
            <h2 class="fw-semibold text-center mb-4">Layanan Digital RW015</h2>
        </div>

        <?php
        $layanan = [
            ['icon' => 'bi-people-fill',      'judul' => 'Administrasi Penduduk',  'deskripsi' => 'Data kependudukan warga RW015',          'url' => 'penduduk'],
            ['icon' => 'bi-envelope-fill',     'judul' => 'Surat Menyurat',         'deskripsi' => 'Pengajuan dan penerbitan surat resmi',    'url' => 'surat'],
            ['icon' => 'bi-cash-stack',        'judul' => 'Keuangan RW',            'deskripsi' => 'Transparansi kas dan laporan keuangan',   'url' => 'keuangan'],
            ['icon' => 'bi-megaphone-fill',    'judul' => 'Pengaduan Warga',        'deskripsi' => 'Sampaikan aspirasi dan pengaduan',        'url' => 'pengaduan'],
            ['icon' => 'bi-calendar-event',    'judul' => 'Kegiatan RW',            'deskripsi' => 'Jadwal dan informasi kegiatan warga',     'url' => 'kegiatan'],
            ['icon' => 'bi-shop',              'judul' => 'UMKM',                   'deskripsi' => 'Direktori usaha mikro warga RW015',       'url' => 'umkm'],
            ['icon' => 'bi-heart-pulse-fill',  'judul' => 'Posyandu',               'deskripsi' => 'Layanan kesehatan ibu dan anak',          'url' => 'posyandu'],
            ['icon' => 'bi-shield-fill-check', 'judul' => 'Keamanan',               'deskripsi' => 'Laporan keamanan dan jadwal ronda',       'url' => 'keamanan'],
            ['icon' => 'bi-bar-chart-fill',    'judul' => 'Statistik',              'deskripsi' => 'Data statistik kependudukan RW015',       'url' => 'statistik'],
        ];
        foreach ($layanan as $item):
        ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="<?= url($item['url']) ?>" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm text-center p-3 card-hover">
                    <div class="card-body">
                        <i class="bi <?= $item['icon'] ?> fs-1 text-primary mb-3 d-block"></i>
                        <h6 class="card-title fw-semibold"><?= $item['judul'] ?></h6>
                        <p class="card-text small text-muted"><?= $item['deskripsi'] ?></p>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

</div>
