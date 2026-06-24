<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Statistik Wilayah</h4>
        <a href="<?= url('api/statistik') ?>" class="btn btn-outline-primary btn-sm" target="_blank">Lihat API</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total Rumah</div><div class="fs-3 fw-bold"><?= number_format($totalRumah) ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total UMKM</div><div class="fs-3 fw-bold"><?= number_format($totalUmkm) ?></div></div></div></div>
        <?php foreach ($gender as $row): ?>
            <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Jenis Kelamin <?= e((string) $row['jenis_kelamin']) ?></div><div class="fs-3 fw-bold"><?= number_format((int) $row['total']) ?></div></div></div></div>
        <?php endforeach; ?>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Sebaran Penduduk per RT</div>
        <div class="table-responsive">
            <table class="table mb-0"><thead><tr><th>RT</th><th>Total Penduduk</th></tr></thead><tbody><?php foreach ($rtStats as $row): ?><tr><td>RT <?= e((string) $row['rt']) ?></td><td><?= number_format((int) $row['total']) ?></td></tr><?php endforeach; ?></tbody></table>
        </div>
    </div>
</div>
