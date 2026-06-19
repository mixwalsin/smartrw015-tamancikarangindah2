<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-map me-2 text-primary"></i>Dashboard RT</h4>
        <a href="<?= url('pengaduan') ?>" class="btn btn-outline-primary btn-sm">Pengaduan Baru</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total Penduduk</div><div class="fs-4 fw-bold"><?= number_format($totalPenduduk) ?></div></div></div></div>
        <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Pengaduan Baru</div><div class="fs-4 fw-bold"><?= number_format($totalPengaduan) ?></div></div></div></div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Kegiatan Mendatang</div>
        <ul class="list-group list-group-flush">
            <?php foreach ($kegiatan as $item): ?>
                <li class="list-group-item d-flex justify-content-between"><span><?= e($item['judul']) ?></span><span class="text-muted small"><?= formatDate((string) $item['tanggal']) ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
