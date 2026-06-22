<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; background: #f5f5f5; }

        .print-wrapper {
            max-width: 21cm;
            margin: 20px auto;
            background: white;
            padding: 0;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .surat-container {
            padding: 2cm 2.5cm 2cm 3cm;
            min-height: 29.7cm;
            position: relative;
        }

        /* KOP SURAT */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .kop-logo {
            width: 80px;
            height: 80px;
            border: 2px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            flex-shrink: 0;
        }
        .kop-text { flex: 1; text-align: center; }
        .kop-text .judul-rw { font-size: 16pt; font-weight: bold; }
        .kop-text .sub-judul { font-size: 12pt; font-weight: bold; }
        .kop-text .alamat { font-size: 9pt; margin-top: 4px; }

        /* JUDUL SURAT */
        .judul-surat {
            text-align: center;
            margin: 25px 0 15px;
        }
        .judul-surat .jenis { font-size: 13pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
        .judul-surat .nomor { font-size: 10pt; margin-top: 4px; }

        /* BADAN SURAT */
        .pembuka { margin-bottom: 15px; }
        .isi-surat { white-space: pre-wrap; line-height: 1.8; margin: 20px 0; }
        .penutup { margin-top: 15px; line-height: 1.8; }

        /* TANDA TANGAN */
        .ttd-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .ttd-kiri { width: 45%; }
        .ttd-kanan { width: 45%; text-align: center; }
        .ttd-kanan .kota-tgl { margin-bottom: 5px; }
        .ttd-kanan .jabatan { font-weight: bold; }
        .ttd-kanan .ttd-space { height: 70px; }
        .ttd-kanan .nama { font-weight: bold; text-decoration: underline; }
        .ttd-kanan .nip { font-size: 10pt; }

        /* QR CODE AREA */
        .qr-section {
            position: absolute;
            bottom: 2cm;
            right: 2.5cm;
            text-align: center;
        }
        .qr-section .qr-label { font-size: 8pt; margin-top: 5px; color: #555; }
        .qr-section .kode-verif { font-size: 7pt; font-family: monospace; color: #777; }

        /* FOOTER SURAT */
        .footer-surat {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            font-size: 8pt;
            color: #666;
            text-align: center;
        }

        /* Print Controls (tidak tercetak) */
        .print-controls {
            background: #343a40;
            padding: 12px 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .print-controls button, .print-controls a {
            padding: 6px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print { background: #198754; color: white; }
        .btn-back  { background: #6c757d; color: white; }
        .status-valid { background: #0dcaf0; color: #000; padding: 4px 10px; border-radius: 4px; font-size: 12px; }

        @media print {
            .print-controls { display: none !important; }
            body { background: white; }
            .print-wrapper { margin: 0; box-shadow: none; }
            .surat-container { padding: 1.5cm 2cm 1.5cm 2.5cm; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>

<!-- Kontrol Cetak (tidak tercetak) -->
<div class="print-controls">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    <a href="<?= url('surat/show/' . $pengajuan['id']) ?>" class="btn-back">← Kembali</a>
    <span class="status-valid">✓ Surat Resmi &mdash; <?= e($pengajuan['jenis_nama']) ?></span>
    <?php if ($pengajuan['no_surat']): ?>
        <span style="color:#adb5bd;font-size:12px">No: <?= e($pengajuan['no_surat']) ?></span>
    <?php endif; ?>
</div>

<!-- Surat -->
<div class="print-wrapper">
    <div class="surat-container">

        <!-- KOP SURAT -->
        <div class="kop-surat">
            <div class="kop-logo">RW<br>015</div>
            <div class="kop-text">
                <div class="judul-rw">RUKUN WARGA 015</div>
                <div class="sub-judul">TAMAN CIKARANG INDAH 2</div>
                <div class="alamat">
                    Kelurahan Taman Cikarang Indah, Kecamatan Cikarang Selatan, Kabupaten Bekasi, Jawa Barat
                </div>
            </div>
        </div>

        <!-- JUDUL SURAT -->
        <div class="judul-surat">
            <div class="jenis"><?= e($pengajuan['jenis_nama']) ?></div>
            <?php if ($pengajuan['no_surat']): ?>
                <div class="nomor">Nomor: <?= e($pengajuan['no_surat']) ?></div>
            <?php endif; ?>
        </div>

        <!-- PEMBUKA -->
        <div class="pembuka">
            Yang bertanda tangan di bawah ini Ketua RW 015 Taman Cikarang Indah 2,
            Kelurahan Taman Cikarang Indah, Kecamatan Cikarang Selatan, Kabupaten Bekasi,
            Provinsi Jawa Barat, dengan ini menerangkan bahwa:
        </div>

        <!-- DATA PEMOHON -->
        <table style="width:100%;border-collapse:collapse;margin:15px 0;line-height:2;">
            <tr><td style="width:160px">Nama Lengkap</td><td style="width:10px">:</td><td><strong><?= e($pengajuan['pemohon_nama']) ?></strong></td></tr>
            <tr><td>NIK</td><td>:</td><td><?= e($pengajuan['pemohon_nik']) ?></td></tr>
            <?php if ($pengajuan['pemohon_tempat_lahir'] || $pengajuan['pemohon_tgl_lahir']): ?>
            <tr>
                <td>Tempat / Tgl. Lahir</td><td>:</td>
                <td>
                    <?= e($pengajuan['pemohon_tempat_lahir'] ?? '-') ?>,
                    <?= $pengajuan['pemohon_tgl_lahir'] ? formatDate($pengajuan['pemohon_tgl_lahir']) : '-' ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($pengajuan['pemohon_jk']): ?>
            <tr><td>Jenis Kelamin</td><td>:</td><td><?= $pengajuan['pemohon_jk'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
            <?php endif; ?>
            <?php if ($pengajuan['pemohon_agama']): ?>
            <tr><td>Agama</td><td>:</td><td><?= e($pengajuan['pemohon_agama']) ?></td></tr>
            <?php endif; ?>
            <?php if ($pengajuan['pemohon_pekerjaan']): ?>
            <tr><td>Pekerjaan</td><td>:</td><td><?= e($pengajuan['pemohon_pekerjaan']) ?></td></tr>
            <?php endif; ?>
            <tr>
                <td>Alamat</td><td>:</td>
                <td><?= e($pengajuan['pemohon_alamat']) ?>, RT <?= e($pengajuan['pemohon_rt']) ?>/RW015</td>
            </tr>
        </table>

        <!-- ISI SURAT -->
        <div class="penutup">
            <?= nl2br(e($penutupSurat)) ?>
        </div>

        <div class="penutup" style="margin-top:15px;">
            Demikian surat keterangan ini dibuat dengan sebenarnya untuk digunakan sebagaimana mestinya.
        </div>

        <!-- TANDA TANGAN -->
        <div class="ttd-section">
            <div class="ttd-kiri">
                <?php if ($pengajuan['rw_approval_at']): ?>
                <div style="font-size:10pt;color:#555;">
                    Disetujui: <?= e(date('d', strtotime($pengajuan['rw_approval_at']))) ?>
                    <?= formatDate($pengajuan['rw_approval_at'], 'F Y') ?><br>
                    Oleh: <?= e($pengajuan['rw_approval_nama'] ?? 'Ketua RW 015') ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="ttd-kanan">
                <div class="kota-tgl">
                    Bekasi, <?= formatDate(date('Y-m-d')) ?>
                </div>
                <div class="jabatan">Ketua RW 015</div>
                <div class="jabatan">Taman Cikarang Indah 2</div>
                <div class="ttd-space"></div>
                <div class="nama">( ________________________ )</div>
            </div>
        </div>

        <!-- QR CODE -->
        <div class="qr-section">
            <div id="qr-print" style="display:inline-block;"></div>
            <div class="qr-label">Scan untuk verifikasi</div>
            <div class="kode-verif"><?= e($pengajuan['kode_verifikasi']) ?></div>
        </div>

        <!-- FOOTER -->
        <div class="footer-surat">
            Surat ini diterbitkan secara digital oleh Sistem Smart RW015.
            Keaslian surat dapat diverifikasi di: <?= e($verifyUrl) ?>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById('qr-print'), {
    text: '<?= e($verifyUrl) ?>',
    width: 90,
    height: 90,
    colorDark: '#000',
    colorLight: '#fff',
    correctLevel: QRCode.CorrectLevel.M
});
</script>
</body>
</html>
