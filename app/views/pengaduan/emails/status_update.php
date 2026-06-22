<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Status Pengaduan</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Update Pengaduan Warga</h2>
    <p>Halo <?= e($pengaduan['pelapor_nama'] ?? 'Warga') ?>,</p>
    <p>Status pengaduan Anda dengan nomor tiket <strong><?= e($pengaduan['no_tiket'] ?? '-') ?></strong> telah diperbarui menjadi <strong><?= e($statusLabel) ?></strong>.</p>
    <p><strong>Judul:</strong> <?= e($pengaduan['judul'] ?? '-') ?></p>
    <p><strong>Lokasi:</strong> <?= e($pengaduan['lokasi'] ?? '-') ?></p>
    <p>Silakan masuk ke dashboard RW Digital untuk melihat detail, timeline komentar, dan tindak lanjut terbaru dari RT/RW.</p>
    <p>Terima kasih.</p>
</body>
</html>
