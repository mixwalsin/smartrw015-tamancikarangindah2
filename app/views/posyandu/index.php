<!-- Dashboard Posyandu -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-heart-pulse me-2 text-danger"></i>Dashboard Posyandu</h4>
        <span class="text-muted small"><?= formatDate(date('Y-m-d'), 'd F Y') ?></span>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg,#4f8ef7,#2563eb); color:#fff;">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-emoji-smile-fill fs-2 opacity-75"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= number_format($totalBalita ?? 0) ?></div>
                        <div class="small opacity-75">Total Balita</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white border-opacity-25 py-1">
                    <a href="<?= url('posyandu/balita') ?>" class="text-white small text-decoration-none">
                        Kelola Balita <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg,#f472b6,#db2777); color:#fff;">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-person-heart fs-2 opacity-75"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= number_format($totalIbuHamil ?? 0) ?></div>
                        <div class="small opacity-75">Ibu Hamil</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white border-opacity-25 py-1">
                    <a href="<?= url('posyandu/ibu-hamil') ?>" class="text-white small text-decoration-none">
                        Kelola Ibu Hamil <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg,#34d399,#059669); color:#fff;">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-calendar-check-fill fs-2 opacity-75"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= number_format($jadwalBulanIni ?? 0) ?></div>
                        <div class="small opacity-75">Jadwal Bulan Ini</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white border-opacity-25 py-1">
                    <a href="<?= url('posyandu/jadwal') ?>" class="text-white small text-decoration-none">
                        Lihat Jadwal <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg,#fbbf24,#d97706); color:#fff;">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-exclamation-triangle-fill fs-2 opacity-75"></i>
                    <div>
                        <div class="fs-4 fw-bold"><?= number_format($giziKurang ?? 0) ?></div>
                        <div class="small opacity-75">Balita Gizi Kurang</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white border-opacity-25 py-1">
                    <a href="<?= url('posyandu/timbangan') ?>" class="text-white small text-decoration-none">
                        Data Timbangan <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert risiko -->
    <?php if (($berisiko ?? 0) > 0): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i class="bi bi-heart-fill fs-4"></i>
            <div>
                <strong><?= $berisiko ?> ibu hamil</strong> berstatus berisiko tinggi.
                <a href="<?= url('posyandu/ibu-hamil') ?>" class="alert-link ms-1">Lihat data &rarr;</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Menu Navigasi Cepat -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="fw-semibold text-muted mb-3">Menu Posyandu</h6>
        </div>
        <?php
        $menus = [
            ['url' => 'posyandu/balita',    'icon' => 'bi-emoji-smile',      'label' => 'Data Balita',         'color' => 'primary'],
            ['url' => 'posyandu/ibu-hamil', 'icon' => 'bi-person-heart',     'label' => 'Ibu Hamil',           'color' => 'danger'],
            ['url' => 'posyandu/jadwal',    'icon' => 'bi-calendar3',        'label' => 'Jadwal Posyandu',     'color' => 'success'],
            ['url' => 'posyandu/imunisasi', 'icon' => 'bi-shield-plus',      'label' => 'Imunisasi',           'color' => 'info'],
            ['url' => 'posyandu/timbangan', 'icon' => 'bi-speedometer2',     'label' => 'Penimbangan',         'color' => 'warning'],
            ['url' => 'posyandu/grafik',    'icon' => 'bi-graph-up-arrow',   'label' => 'Grafik Pertumbuhan',  'color' => 'secondary'],
        ];
        foreach ($menus as $menu):
        ?>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= url($menu['url']) ?>" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body text-center py-3">
                    <i class="bi <?= $menu['icon'] ?> fs-2 text-<?= $menu['color'] ?>"></i>
                    <div class="mt-2 small fw-semibold text-dark"><?= $menu['label'] ?></div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Jadwal Mendatang -->
    <?php if (!empty($jadwalMendatang)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom fw-semibold">
            <i class="bi bi-calendar-event text-success me-2"></i>Jadwal Posyandu Mendatang
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php foreach ($jadwalMendatang as $j): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold"><?= e($j['lokasi']) ?></div>
                            <small class="text-muted">
                                <?= formatDate($j['tanggal']) ?> &bull;
                                <?= substr($j['jam_mulai'], 0, 5) ?>&ndash;<?= substr($j['jam_selesai'], 0, 5) ?>
                            </small>
                            <?php if (!empty($j['keterangan'])): ?>
                                <div class="text-muted small"><?= e($j['keterangan']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php
                        $badgeMap = [
                            'dijadwalkan' => 'bg-primary',
                            'berlangsung' => 'bg-success',
                            'selesai'     => 'bg-secondary',
                            'dibatalkan'  => 'bg-danger',
                        ];
                        $badge = $badgeMap[$j['status']] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?= $badge ?>"><?= ucfirst($j['status']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="card-footer bg-white">
            <a href="<?= url('posyandu/jadwal') ?>" class="btn btn-sm btn-outline-success">Lihat Semua Jadwal</a>
        </div>
    </div>
    <?php endif; ?>

</div>
