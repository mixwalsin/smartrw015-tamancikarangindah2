<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
    <i class="bi bi-exclamation-triangle text-warning" style="font-size:5rem"></i>
    <h1 class="display-4 fw-bold mt-3">404</h1>
    <p class="lead text-muted">Halaman yang Anda cari tidak ditemukan.</p>
    <a href="<?= url('/') ?>" class="btn btn-primary mt-2">
        <i class="bi bi-house me-1"></i>Kembali ke Beranda
    </a>
</div>
</body>
</html>
