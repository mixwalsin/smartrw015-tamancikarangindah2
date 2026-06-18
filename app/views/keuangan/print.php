<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= e($title ?? 'Cetak Laporan Kas') ?></title>
    <style>
        body{font-family:Arial,sans-serif;font-size:12px} table{width:100%;border-collapse:collapse;margin-top:10px} th,td{border:1px solid #ccc;padding:6px}
    </style>
</head>
<body onload="window.print()">
<h2><?= e($title ?? 'Cetak Laporan Kas') ?></h2>
<p>Pemasukan: <?= rupiah((float) $report['total_pemasukan']) ?> | Pengeluaran: <?= rupiah((float) $report['total_pengeluaran']) ?> | Saldo: <?= rupiah((float) $report['saldo']) ?></p>
<table>
    <thead><tr><th>Tanggal</th><th>Kas</th><th>Jenis</th><th>Kategori</th><th>Nominal</th><th>Status</th><th>Deskripsi</th></tr></thead>
    <tbody>
    <?php foreach ($report['items'] as $item): ?>
        <tr>
            <td><?= e((string) $item['date']) ?></td>
            <td><?= e(strtoupper((string) $item['kas_type'])) ?></td>
            <td><?= e((string) $item['transaction_type']) ?></td>
            <td><?= e((string) ($item['category_name'] ?? '-')) ?></td>
            <td><?= rupiah((float) $item['amount']) ?></td>
            <td><?= e((string) $item['status']) ?></td>
            <td><?= e((string) ($item['description'] ?? '')) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
