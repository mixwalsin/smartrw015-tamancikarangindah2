<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= url('/') ?>">
            <i class="bi bi-building me-1"></i> Smart RW015
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('dashboard') ?>"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('penduduk') ?>"><i class="bi bi-people me-1"></i>Penduduk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('surat') ?>"><i class="bi bi-envelope me-1"></i>Surat</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-grid me-1"></i>Layanan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('keuangan') ?>"><i class="bi bi-cash-stack me-1"></i>Keuangan</a></li>
                        <li><a class="dropdown-item" href="<?= url('pengaduan') ?>"><i class="bi bi-megaphone me-1"></i>Pengaduan</a></li>
                        <li><a class="dropdown-item" href="<?= url('kegiatan') ?>"><i class="bi bi-calendar-event me-1"></i>Kegiatan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= url('umkm') ?>"><i class="bi bi-shop me-1"></i>UMKM</a></li>
                        <li><a class="dropdown-item" href="<?= url('posyandu') ?>"><i class="bi bi-heart-pulse me-1"></i>Posyandu</a></li>
                        <li><a class="dropdown-item" href="<?= url('keamanan') ?>"><i class="bi bi-shield-check me-1"></i>Keamanan</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('statistik') ?>"><i class="bi bi-bar-chart me-1"></i>Statistik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('laporan') ?>"><i class="bi bi-file-earmark-text me-1"></i>Laporan</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <?php $user = authUser(); ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= e($user['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small"><?= e($user['role']) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('profil') ?>"><i class="bi bi-person me-1"></i>Profil</a></li>
                            <li><a class="dropdown-item text-danger" href="<?= url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-1"></i>Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('auth/login') ?>"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<div class="container-fluid mt-2">
    <?php foreach (['success', 'error', 'warning', 'info'] as $type): ?>
        <?php $flash = getFlash($type); ?>
        <?php if ($flash !== null): ?>
            <?php $bsType = $type === 'error' ? 'danger' : $type; ?>
            <div class="alert alert-<?= $bsType ?> alert-dismissible fade show" role="alert">
                <?= e($flash) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Main Content -->
<main class="container-fluid py-3">
    <?= $content ?>
</main>

<!-- Footer -->
<footer class="bg-light border-top mt-auto py-3">
    <div class="container-fluid text-center text-muted small">
        &copy; <?= date('Y') ?> <?= APP_NAME ?> &mdash; v<?= APP_VERSION ?>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
