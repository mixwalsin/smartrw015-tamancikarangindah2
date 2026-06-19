<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-primary"></i>Dashboard Security</h4>
        <span class="text-muted small"><?= formatDate(date('Y-m-d')) ?></span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Tamu Hari Ini</div>
                    <div class="fs-3 fw-bold"><?= number_format((int) ($stats['tamu_hari_ini'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Kendaraan Hari Ini</div>
                    <div class="fs-3 fw-bold"><?= number_format((int) ($stats['kendaraan_hari_ini'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Insiden Pending</div>
                    <div class="fs-3 fw-bold text-danger"><?= number_format((int) ($stats['insiden_pending'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap gap-2">
            <a href="<?= url('security/tamu') ?>" class="btn btn-outline-primary btn-sm">Tamu</a>
            <a href="<?= url('security/kendaraan') ?>" class="btn btn-outline-primary btn-sm">Kendaraan</a>
            <a href="<?= url('security/insiden') ?>" class="btn btn-outline-primary btn-sm">Insiden</a>
            <a href="<?= url('security/patroli') ?>" class="btn btn-outline-primary btn-sm">Patroli</a>
            <a href="<?= url('security/qr-pass') ?>" class="btn btn-outline-primary btn-sm">QR Pass</a>
            <a href="<?= url('security/check-in-out') ?>" class="btn btn-outline-primary btn-sm">Check-In/Out</a>
            <a href="<?= url('security/laporan') ?>" class="btn btn-outline-primary btn-sm">Laporan</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">Aktivitas Terbaru</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($aktivitasTerbaru ?? [] as $item): ?>
                            <li class="list-group-item">
                                <div class="fw-semibold"><?= e((string) ($item['deskripsi'] ?? '')) ?></div>
                                <small class="text-muted"><?= e((string) ($item['created_at'] ?? '')) ?></small>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($aktivitasTerbaru)): ?>
                            <li class="list-group-item text-muted">Belum ada aktivitas.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">Insiden Pending</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($insidenPending ?? [] as $item): ?>
                            <li class="list-group-item">
                                <div class="fw-semibold"><?= e((string) ($item['tipe_insiden'] ?? '-')) ?> - <?= e((string) ($item['lokasi'] ?? '-')) ?></div>
                                <small class="text-muted"><?= e((string) ($item['status'] ?? 'baru')) ?></small>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($insidenPending)): ?>
                            <li class="list-group-item text-muted">Tidak ada insiden pending.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
