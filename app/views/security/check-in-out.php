<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Check-In / Check-Out</h4>
        <a href="<?= url('security') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Tamu Belum Check-Out</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($pendingTamu ?? [] as $row): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold"><?= e((string) ($row['nama'] ?? '-')) ?></div>
                                <small class="text-muted"><?= e((string) ($row['jam_masuk'] ?? '-')) ?></small>
                            </div>
                            <form method="POST" action="<?= url('security/tamu/checkout/' . (int) ($row['id'] ?? 0)) ?>">
                                <?= csrfField() ?>
                                <button class="btn btn-sm btn-outline-danger">Check-Out</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($pendingTamu)): ?>
                        <li class="list-group-item text-muted">Tidak ada tamu pending.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Kendaraan Parkir</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($kendaraanParkir ?? [] as $row): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold"><code><?= e((string) ($row['no_plat'] ?? '-')) ?></code></div>
                                <small class="text-muted"><?= e((string) ($row['jam_masuk'] ?? '-')) ?></small>
                            </div>
                            <form method="POST" action="<?= url('security/kendaraan/checkout/' . (int) ($row['id'] ?? 0)) ?>">
                                <?= csrfField() ?>
                                <button class="btn btn-sm btn-outline-danger">Check-Out</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($kendaraanParkir)): ?>
                        <li class="list-group-item text-muted">Tidak ada kendaraan parkir.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
