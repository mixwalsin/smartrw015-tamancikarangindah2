<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Dashboard RW</h4>
        <a href="<?= url('laporan') ?>" class="btn btn-outline-primary btn-sm">Laporan</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total Penduduk</div><div class="fs-4 fw-bold"><?= number_format($totalPenduduk) ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Pengaduan</div><div class="fs-4 fw-bold"><?= number_format($totalPengaduan) ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Saldo Kas RW</div><div class="fs-4 fw-bold"><?= rupiah($saldoKasRw) ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Akumulasi Kas RT</div><div class="fs-4 fw-bold"><?= rupiah($saldoKasRt) ?></div></div></div></div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Agenda Pengurus Terdekat</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Judul</th><th>Tanggal</th><th>Lokasi</th></tr></thead>
                <tbody>
                    <?php foreach ($kegiatanTerbaru as $kegiatan): ?>
                        <tr><td><?= e($kegiatan['judul']) ?></td><td><?= formatDate((string) $kegiatan['tanggal']) ?></td><td><?= e((string) $kegiatan['lokasi']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
