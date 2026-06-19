<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= e($title ?? 'Cetak Surat') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #111; }
        .header, .footer { text-align: center; }
        .content { margin-top: 30px; line-height: 1.7; }
    </style>
</head>
<body onload="window.print()">
<div class="header">
    <h2>SMART RW015 TAMAN CIKARANG INDAH 2</h2>
    <p>Pelayanan Surat Online RW015</p>
    <hr>
</div>
<div class="content">
    <p><strong>Nomor Surat:</strong> <?= e((string) ($row['no_surat'] ?? '-')) ?></p>
    <p><strong>Jenis Surat:</strong> <?= e((string) ($row['jenis_surat'] ?? '-')) ?></p>
    <p><strong>Nama Warga:</strong> <?= e((string) ($row['nama_warga'] ?? '-')) ?></p>
    <p><strong>NIK:</strong> <?= e((string) ($row['nik'] ?? '-')) ?></p>
    <p><strong>Alamat:</strong> <?= e((string) ($row['alamat'] ?? '-')) ?> RT <?= e((string) ($row['rt'] ?? '-')) ?></p>
    <p><strong>Keperluan:</strong> <?= nl2br(e((string) ($row['keperluan'] ?? '-'))) ?></p>
    <p><strong>Status:</strong> <?= e((string) ($row['status'] ?? '-')) ?></p>
</div>
<div class="footer">
    <p>Dicetak pada <?= e(formatDate(date('Y-m-d'))) ?></p>
</div>
</body>
</html>
