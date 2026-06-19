<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= url('/') ?>"><i class="bi bi-building me-1"></i> Smart RW015</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= url('dashboard') ?>">Dashboard</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Master Data</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('penduduk') ?>">Penduduk</a></li>
                        <li><a class="dropdown-item" href="<?= url('kk') ?>">Kartu Keluarga</a></li>
                        <li><a class="dropdown-item" href="<?= url('rumah') ?>">Rumah</a></li>
                        <li><a class="dropdown-item" href="<?= url('umkm') ?>">UMKM</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Layanan</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('surat') ?>">Surat Online</a></li>
                        <li><a class="dropdown-item" href="<?= url('pengaduan') ?>">Pengaduan</a></li>
                        <li><a class="dropdown-item" href="<?= url('keuangan') ?>">Kas RW / RT</a></li>
                        <li><a class="dropdown-item" href="<?= url('kegiatan') ?>">Kegiatan</a></li>
                        <li><a class="dropdown-item" href="<?= url('posyandu') ?>">Posyandu</a></li>
                        <li><a class="dropdown-item" href="<?= url('keamanan') ?>">Security</a></li>
                        <li><a class="dropdown-item" href="<?= url('pengumuman') ?>">Pengumuman</a></li>
                        <li><a class="dropdown-item" href="<?= url('laporan') ?>">Laporan PDF/Excel</a></li>
                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw'], true)): ?>
                            <li><a class="dropdown-item" href="<?= url('audit-log') ?>">Audit Log</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?= url('statistik') ?>">Statistik</a></li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link position-relative" href="<?= url('notifikasi') ?>"><i class="bi bi-bell"></i><?php if (notificationCount() > 0): ?><span class="badge rounded-pill bg-danger ms-1"><?= notificationCount() ?></span><?php endif; ?></a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-person-circle me-1"></i><?= e(authUser()['name'] ?? 'User') ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small"><?= e(authUser()['role_name'] ?? authUser()['role'] ?? '') ?></span></li>
                            <li><a class="dropdown-item" href="<?= url('profil') ?>">Profil</a></li>
                            <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw'], true)): ?><li><a class="dropdown-item" href="<?= url('admin/users') ?>">Manajemen Pengguna</a></li><?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= url('auth/logout') ?>">Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('auth/login') ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid mt-3">
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
<main class="container-fluid py-2"><?= $content ?></main>
<footer class="bg-white border-top mt-4 py-3"><div class="container-fluid text-center text-muted small">&copy; <?= date('Y') ?> <?= APP_NAME ?> &mdash; v<?= APP_VERSION ?> &mdash; XAMPP Ready</div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
