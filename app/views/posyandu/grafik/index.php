<!-- Grafik Pertumbuhan - Pilih Balita -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('posyandu') ?>" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
            <span class="fs-5 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-secondary"></i>Grafik Pertumbuhan</span>
        </div>
    </div>

    <div class="row g-3">
        <?php if (!empty($balitas)): ?>
            <?php foreach ($balitas as $b): ?>
                <?php
                $tglLahir = new \DateTime($b['tgl_lahir']);
                $now      = new \DateTime();
                $diff     = $tglLahir->diff($now);
                $umurTeks = ($diff->y > 0 ? $diff->y . ' th ' : '') . $diff->m . ' bln';
                ?>
                <div class="col-md-4 col-lg-3">
                    <a href="<?= url('posyandu/grafik/' . $b['id']) ?>" class="card border-0 shadow-sm h-100 text-decoration-none">
                        <div class="card-body text-center py-3">
                            <div class="display-6 mb-2"><?= $b['jenis_kelamin'] === 'L' ? '👦' : '👧' ?></div>
                            <div class="fw-semibold"><?= e($b['nama']) ?></div>
                            <div class="text-muted small"><?= $umurTeks ?> &bull; RT <?= e($b['rt']) ?></div>
                            <div class="mt-2">
                                <?php if ($b['berat_badan'] !== null): ?>
                                    <span class="badge bg-primary"><?= number_format((float)$b['berat_badan'], 1) ?> kg</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent text-center small text-primary">
                            <i class="bi bi-graph-up me-1"></i>Lihat Grafik
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <i class="bi bi-emoji-smile display-3 d-block mb-3 opacity-25"></i>
                    Belum ada data balita. <a href="<?= url('posyandu/balita/create') ?>">Tambah balita</a> terlebih dahulu.
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
