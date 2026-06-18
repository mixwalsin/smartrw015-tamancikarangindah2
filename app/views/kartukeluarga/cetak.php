<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Cetak KK') ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header h2 { font-size: 14pt; text-transform: uppercase; letter-spacing: 1px; }
        .header p  { font-size: 10pt; margin-top: 4px; }
        .kk-info {
            border: 1px solid #333;
            padding: 10px 16px;
            margin-bottom: 16px;
            background: #f9f9f9;
        }
        .kk-info table { width: 100%; border-collapse: collapse; }
        .kk-info td { padding: 3px 6px; font-size: 10.5pt; }
        .kk-info td:first-child { width: 40%; font-weight: bold; }
        .kk-number {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 2px;
            color: #000;
        }
        h4 { font-size: 11pt; margin-bottom: 6px; }
        table.anggota {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        table.anggota th, table.anggota td {
            border: 1px solid #555;
            padding: 5px 7px;
            text-align: left;
        }
        table.anggota th {
            background: #ddd;
            font-weight: bold;
        }
        table.anggota tr:nth-child(even) td { background: #f5f5f5; }
        .footer {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 10pt;
        }
        .sign-block { text-align: center; width: 200px; }
        .sign-block .line { border-top: 1px solid #000; margin-top: 48px; }
        .print-btn {
            position: fixed;
            top: 16px; right: 16px;
            background: #0d6efd;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11pt;
        }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">🖨 Cetak</button>

<div class="header">
    <h2>Kartu Keluarga (Internal)</h2>
    <p>RW 015 Taman Cikarang Indah 2 &bull; Kel. Taman Cikarang Indah, Kec. Cikarang Selatan, Kab. Bekasi, Jawa Barat</p>
</div>

<div class="kk-info">
    <table>
        <tr>
            <td>Nomor KK</td>
            <td class="kk-number"><?= e($kk['no_kk']) ?></td>
        </tr>
        <tr>
            <td>Kepala Keluarga</td>
            <td><?= e($kk['kepala_keluarga'] ?? '—') ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td><?= e($kk['alamat']) ?></td>
        </tr>
        <tr>
            <td>RT / RW</td>
            <td>RT <?= e($kk['rt_text'] ?? '—') ?> / RW 015</td>
        </tr>
        <tr>
            <td>Kelurahan</td>
            <td>Taman Cikarang Indah 2</td>
        </tr>
        <tr>
            <td>Kecamatan</td>
            <td>Cikarang Selatan</td>
        </tr>
        <tr>
            <td>Kabupaten / Kota</td>
            <td>Bekasi</td>
        </tr>
        <tr>
            <td>Provinsi</td>
            <td>Jawa Barat</td>
        </tr>
        <tr>
            <td>Jumlah Anggota</td>
            <td><?= count($anggota) ?> orang</td>
        </tr>
    </table>
</div>

<h4>Daftar Anggota Keluarga</h4>
<table class="anggota">
    <thead>
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Nama Lengkap</th>
            <th>Hubungan</th>
            <th>JK</th>
            <th>Tempat, Tgl. Lahir</th>
            <th>Agama</th>
            <th>Pendidikan</th>
            <th>Pekerjaan</th>
            <th>Status Kawin</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($anggota)): ?>
            <?php foreach ($anggota as $i => $a): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($a['nik']) ?></td>
                    <td><?= e($a['nama']) ?></td>
                    <td><?= e($a['hubungan']) ?></td>
                    <td><?= $a['jenis_kelamin'] === 'L' ? 'L' : 'P' ?></td>
                    <td>
                        <?= e($a['tempat_lahir'] ?? '—') ?>,
                        <?= $a['tanggal_lahir'] ? e(formatDate($a['tanggal_lahir'])) : '—' ?>
                    </td>
                    <td><?= e($a['agama'] ?? '—') ?></td>
                    <td><?= e($a['pendidikan'] ?? '—') ?></td>
                    <td><?= e($a['pekerjaan'] ?? '—') ?></td>
                    <td><?= e($a['status_kawin'] ?? '—') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="10" style="text-align:center">Belum ada anggota.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    <div>
        <p>Dicetak oleh sistem Smart RW015</p>
        <p>Tanggal cetak: <?= date('d F Y, H:i') ?> WIB</p>
        <p><em>Dokumen ini hanya untuk keperluan internal RW015 dan tidak menggantikan KK resmi yang dikeluarkan oleh Dinas Kependudukan dan Pencatatan Sipil.</em></p>
    </div>
    <div class="sign-block">
        <p>Mengetahui,</p>
        <p>Ketua RW 015</p>
        <div class="line"></div>
        <p>(...............................)</p>
    </div>
</div>

</body>
</html>
