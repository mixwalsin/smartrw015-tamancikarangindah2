<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Modul Laporan</h4>
        <div class="d-flex gap-2">
            <a href="<?= url('laporan/export?' . http_build_query([
                'modul' => $selectedModule,
                'format' => 'pdf',
                'rt' => $filters['rt'],
                'tanggal' => $filters['tanggal'],
                'bulan' => $filters['bulan'],
                'tahun' => $filters['tahun'],
            ])) ?>" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
            <a href="<?= url('laporan/export?' . http_build_query([
                'modul' => $selectedModule,
                'format' => 'excel',
                'rt' => $filters['rt'],
                'tanggal' => $filters['tanggal'],
                'bulan' => $filters['bulan'],
                'tahun' => $filters['tahun'],
            ])) ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="<?= url('laporan') ?>" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1">Laporan</label>
                    <select name="modul" class="form-select">
                        <?php foreach ($moduleOptions as $moduleKey => $moduleLabel): ?>
                            <option value="<?= e($moduleKey) ?>" <?= $selectedModule === $moduleKey ? 'selected' : '' ?>>
                                <?= e($moduleLabel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">RT</label>
                    <input type="text" class="form-control" name="rt" value="<?= e($filters['rt']) ?>" placeholder="001">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" value="<?= e($filters['tanggal']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Bulan</label>
                    <?php $bulanList = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember']; ?>
                    <select name="bulan" class="form-select">
                        <option value="0">Semua</option>
                        <?php foreach ($bulanList as $angkaBulan => $namaBulan): ?>
                            <option value="<?= $angkaBulan ?>" <?= (int) $filters['bulan'] === $angkaBulan ? 'selected' : '' ?>><?= $namaBulan ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label mb-1">Tahun</label>
                    <input type="number" class="form-control" name="tahun" value="<?= $filters['tahun'] > 0 ? e((string) $filters['tahun']) : '' ?>" placeholder="<?= date('Y') ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="<?= url('laporan') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <?php foreach ($summary as $item): ?>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small"><?= e($item['label']) ?></div>
                        <div class="fs-4 fw-bold"><?= number_format((int) $item['total']) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">
            Data <?= e($selectedLabel) ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <?php if (!empty($rows)): ?>
                            <tr>
                                <?php foreach (array_keys($rows[0]) as $header): ?>
                                    <th><?= e((string) $header) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?= e(is_scalar($value) || $value === null ? (string) ($value ?? '') : json_encode($value)) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="text-center text-muted py-4">Tidak ada data untuk filter terpilih.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
