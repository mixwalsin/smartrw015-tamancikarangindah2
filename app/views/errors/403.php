<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
    <i class="bi bi-shield-x text-danger" style="font-size:5rem"></i>
    <h1 class="display-4 fw-bold mt-3">403</h1>
    <p class="lead text-muted"><?= e($message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.') ?></p>
    <a href="<?= url('dashboard') ?>" class="btn btn-primary mt-2">
        <i class="bi bi-speedometer2 me-1"></i>Kembali ke Dashboard
    </a>
</div>
</body>
</html>
