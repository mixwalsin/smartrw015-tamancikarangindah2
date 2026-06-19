<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Security</h4>
        <a href="<?= url('security') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="small text-muted">Tamu</div><div class="fs-4 fw-bold"><?= number_format((int) ($daily['tamu'] ?? 0)) ?></div></div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="small text-muted">Kendaraan</div><div class="fs-4 fw-bold"><?= number_format((int) ($daily['kendaraan'] ?? 0)) ?></div></div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="small text-muted">Insiden Pending</div><div class="fs-4 fw-bold text-danger"><?= number_format((int) ($daily['insiden'] ?? 0)) ?></div></div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="small text-muted">Patroli</div><div class="fs-4 fw-bold"><?= number_format((int) ($daily['patroli'] ?? 0)) ?></div></div></div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Insiden per Tipe</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($insidenPerTipe ?? [] as $row): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e((string) ($row['tipe_insiden'] ?? '-')) ?></span>
                            <strong><?= number_format((int) ($row['total'] ?? 0)) ?></strong>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($insidenPerTipe)): ?>
                        <li class="list-group-item text-muted">Belum ada data.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Patroli per Lokasi</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($patroliPerLokasi ?? [] as $row): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e((string) ($row['lokasi_patroli'] ?? '-')) ?></span>
                            <strong><?= number_format((int) ($row['total'] ?? 0)) ?></strong>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($patroliPerLokasi)): ?>
                        <li class="list-group-item text-muted">Belum ada data.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
