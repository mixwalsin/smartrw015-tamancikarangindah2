<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= e($title ?? 'Laporan') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #bbb; padding: 8px; font-size: 12px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body onload="window.print()">
    <h2><?= e($title ?? 'Laporan') ?></h2>
    <p>Ekspor PDF siap cetak / simpan sebagai PDF melalui browser.</p>
    <table>
        <thead>
            <tr>
                <?php if (!empty($rows)): ?>
                    <?php foreach (array_keys($rows[0]) as $key): ?>
                        <th><?= e((string) $key) ?></th>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <?php foreach ($row as $value): ?>
                        <td><?= e((string) $value) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
