<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= e($title ?? 'Laporan Pengaduan') ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; margin: 30px; }
        h1, h2, h3 { margin: 0 0 10px; }
        .muted { color: #666; }
        .box { border: 1px solid #ccc; border-radius: 6px; padding: 16px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        .gallery { display: flex; flex-wrap: wrap; gap: 12px; }
        .gallery img { width: 180px; height: 140px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
    </style>
</head>
<body onload="window.print()">
    <h1>Laporan Pengaduan Warga</h1>
    <p class="muted">Dicetak dari platform <?= e(APP_NAME) ?></p>

    <div class="box">
        <table>
            <tr><th width="30%">No. Tiket</th><td><?= e($pengaduan['no_tiket'] ?? '-') ?></td></tr>
            <tr><th>Judul</th><td><?= e($pengaduan['judul']) ?></td></tr>
            <tr><th>Pelapor</th><td><?= e($pengaduan['pelapor_nama'] ?? '-') ?></td></tr>
            <tr><th>Kategori</th><td><?= e($pengaduan['kategori_nama'] ?? '-') ?></td></tr>
            <tr><th>Status</th><td><?= e($pengaduan['status']) ?></td></tr>
            <tr><th>Prioritas</th><td><?= e($pengaduan['prioritas'] ?? '-') ?></td></tr>
            <tr><th>Lokasi</th><td><?= e($pengaduan['lokasi'] ?? '-') ?></td></tr>
            <tr><th>Dibuat</th><td><?= e(formatDate($pengaduan['created_at'], 'd M Y H:i')) ?></td></tr>
            <tr><th>Deskripsi</th><td><?= nl2br(e($pengaduan['deskripsi'])) ?></td></tr>
        </table>
    </div>

    <?php if (!empty($pengaduan['fotos'])): ?>
        <h3>Dokumentasi Foto</h3>
        <div class="gallery box">
            <?php foreach ($pengaduan['fotos'] as $foto): ?>
                <img src="<?= url('storage/uploads/' . $foto['foto_path']) ?>" alt="Foto pengaduan">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h3>Riwayat Status</h3>
    <div class="box">
        <table>
            <tr><th>Waktu</th><th>Status</th><th>Keterangan</th></tr>
            <?php foreach ($pengaduan['status_history'] as $history): ?>
                <tr>
                    <td><?= e(formatDate($history['changed_at'], 'd M Y H:i')) ?></td>
                    <td><?= e($history['status_baru']) ?></td>
                    <td><?= e($history['keterangan'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
