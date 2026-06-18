<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login - ' . APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="text-center mb-4">
                <i class="bi bi-building text-primary" style="font-size:3rem"></i>
                <h4 class="fw-bold mt-2"><?= e(APP_NAME) ?></h4>
                <p class="text-muted small">Masuk ke akun Anda</p>
            </div>

            <?php $flash = getFlash('error'); ?>
            <?php if ($flash): ?>
                <div class="alert alert-danger"><?= e($flash) ?></div>
            <?php endif; ?>
            <?php $flash = getFlash('success'); ?>
            <?php if ($flash): ?>
                <div class="alert alert-success"><?= e($flash) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="<?= url('auth/login') ?>" method="POST">
                        <?= csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control"
                                       placeholder="Masukkan username" required autofocus
                                       value="<?= e($_POST['username'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control"
                                       placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3 text-muted small">
                Belum punya akun? <a href="<?= url('auth/register') ?>">Daftar di sini</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
