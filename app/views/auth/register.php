<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Daftar - ' . APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="text-center mb-4">
                <i class="bi bi-building text-primary" style="font-size:3rem"></i>
                <h4 class="fw-bold mt-2"><?= e(APP_NAME) ?></h4>
                <p class="text-muted small">Buat akun baru</p>
            </div>

            <?php $flash = getFlash('error'); ?>
            <?php if ($flash): ?>
                <div class="alert alert-danger"><?= e($flash) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="<?= url('auth/register') ?>" method="POST">
                        <?= csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap" required
                                   value="<?= e($_POST['name'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username" required
                                   value="<?= e($_POST['username'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" required
                                   value="<?= e($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Minimal 8 karakter" required minlength="8">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Ulangi password" required minlength="8">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-1"></i>Daftar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3 text-muted small">
                Sudah punya akun? <a href="<?= url('auth/login') ?>">Login di sini</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
