<!-- Grafik Pertumbuhan Balita -->
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="<?= url('posyandu/grafik') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-graph-up-arrow me-2 text-secondary"></i>Grafik Pertumbuhan &mdash; <?= e($balita['nama']) ?>
        </h4>
        <a href="<?= url('posyandu/balita/show/' . $balita['id']) ?>" class="btn btn-sm btn-outline-primary ms-auto">
            <i class="bi bi-person me-1"></i>Detail Balita
        </a>
    </div>

    <!-- Info Balita -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="display-5 mb-1"><?= $balita['jenis_kelamin'] === 'L' ? '👦' : '👧' ?></div>
                <div class="fw-bold"><?= e($balita['nama']) ?></div>
                <?php
                $tglLahir = new \DateTime($balita['tgl_lahir']);
                $now      = new \DateTime();
                $diff     = $tglLahir->diff($now);
                ?>
                <div class="text-muted small"><?= ($diff->y > 0 ? $diff->y . ' th ' : '') . $diff->m . ' bln' ?></div>
                <div class="text-muted small">RT <?= e($balita['rt']) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-1 fw-bold text-primary"><?= $balita['berat_badan'] !== null ? number_format((float)$balita['berat_badan'], 1) : '-' ?></div>
                <div class="text-muted small">BB Terakhir (kg)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-1 fw-bold text-success"><?= $balita['tinggi_badan'] !== null ? number_format((float)$balita['tinggi_badan'], 1) : '-' ?></div>
                <div class="text-muted small">TB Terakhir (cm)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <?php
                $statusMap = [
                    'lengkap'       => ['bg-success', 'Imunisasi Lengkap'],
                    'tidak_lengkap' => ['bg-warning text-dark', 'Belum Lengkap'],
                    'belum'         => ['bg-secondary', 'Belum Imunisasi'],
                ];
                [$badgeCls, $badgeLbl] = $statusMap[$balita['status_imunisasi']] ?? ['bg-secondary', 'Belum'];
                ?>
                <span class="badge <?= $badgeCls ?> fs-6 mt-2"><?= $badgeLbl ?></span>
                <div class="text-muted small mt-2">Status Imunisasi</div>
            </div>
        </div>
    </div>

    <?php if (!empty($timbangan)): ?>

    <!-- Grafik Berat Badan -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-graph-up text-primary me-2"></i>Tren Berat Badan
        </div>
        <div class="card-body">
            <canvas id="chartBB" height="100"></canvas>
        </div>
    </div>

    <!-- Grafik Tinggi Badan -->
    <?php $adaTinggi = array_filter($timbangan, fn($t) => $t['tinggi_badan'] !== null); ?>
    <?php if (!empty($adaTinggi)): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-bar-chart text-success me-2"></i>Tren Tinggi Badan
        </div>
        <div class="card-body">
            <canvas id="chartTB" height="100"></canvas>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabel Data -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-table text-secondary me-2"></i>Data Penimbangan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>BB (kg)</th>
                            <th>TB (cm)</th>
                            <th>Status Gizi</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach (array_reverse($timbangan) as $t): ?>
                            <?php
                            $giziMap = [
                                'gizi_baik'   => 'bg-success',
                                'gizi_kurang' => 'bg-warning text-dark',
                                'gizi_buruk'  => 'bg-danger',
                                'lebih'       => 'bg-info text-dark',
                            ];
                            $giziCls = $giziMap[$t['status_gizi']] ?? 'bg-secondary';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= formatDate($t['tanggal_timbang']) ?></td>
                                <td><?= number_format((float)$t['berat_badan'], 1) ?></td>
                                <td><?= $t['tinggi_badan'] !== null ? number_format((float)$t['tinggi_badan'], 1) : '-' ?></td>
                                <td><span class="badge <?= $giziCls ?>"><?= str_replace('_', ' ', ucfirst($t['status_gizi'])) ?></span></td>
                                <td><?= e($t['catatan'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        const labels  = <?= $labels ?>;
        const berats  = <?= $berats ?>;
        const tinggis = <?= $tinggis ?>;

        // Grafik Berat Badan
        new Chart(document.getElementById('chartBB'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: berats,
                    borderColor: 'rgba(37, 99, 235, 0.9)',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: false, title: { display: true, text: 'kg' } },
                    x: { title: { display: true, text: 'Tanggal' } }
                }
            }
        });

        <?php if (!empty($adaTinggi)): ?>
        // Grafik Tinggi Badan
        new Chart(document.getElementById('chartTB'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tinggi Badan (cm)',
                    data: tinggis,
                    backgroundColor: 'rgba(5, 150, 105, 0.7)',
                    borderColor: 'rgba(5, 150, 105, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: false, title: { display: true, text: 'cm' } },
                    x: { title: { display: true, text: 'Tanggal' } }
                }
            }
        });
        <?php endif; ?>
    })();
    </script>

    <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-graph-up display-3 d-block mb-3 opacity-25"></i>
            Belum ada data timbangan untuk ditampilkan.
            <div class="mt-3">
                <a href="<?= url('posyandu/timbangan/create?balita_id=' . $balita['id']) ?>" class="btn btn-warning">
                    <i class="bi bi-speedometer2 me-1"></i>Catat Timbangan Pertama
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>
