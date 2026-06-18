<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak KK Internal - <?= e($kk['nomor_kk']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="p-4">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Laporan Kartu Keluarga Internal</h4>
        <button class="btn btn-primary no-print" onclick="window.print()">Cetak</button>
    </div>

    <table class="table table-sm table-bordered mb-4">
        <tr><th width="30%">Nomor KK</th><td><?= e($kk['nomor_kk']) ?></td></tr>
        <tr><th>Kepala Keluarga</th><td><?= e($kk['kepala_keluarga']) ?></td></tr>
        <tr><th>Alamat</th><td><?= e($kk['alamat']) ?></td></tr>
        <tr><th>RT / RW</th><td>RT <?= e($kk['rt']) ?> / RW <?= e($kk['rw']) ?></td></tr>
        <tr><th>Jumlah Anggota</th><td><?= (int) $kk['jumlah_anggota'] ?> orang</td></tr>
    </table>

    <h6>Daftar Anggota Keluarga</h6>
    <table class="table table-sm table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Hubungan</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($anggota)): ?>
            <?php foreach ($anggota as $i => $item): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($item['nik'] ?? '-') ?></td>
                    <td><?= e($item['nama'] ?? '-') ?></td>
                    <td><?= e($item['hubungan']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">Belum ada anggota.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
