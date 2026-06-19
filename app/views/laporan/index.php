<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan PDF / Excel</h4>
        <div class="d-flex gap-2">
            <a href="<?= url('laporan/export?module=' . urlencode($module) . '&format=pdf') ?>" class="btn btn-outline-danger btn-sm">PDF</a>
            <a href="<?= url('laporan/export?module=' . urlencode($module) . '&format=excel') ?>" class="btn btn-outline-success btn-sm">Excel / CSV</a>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="<?= url('laporan') ?>" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Modul Laporan</label>
                    <select name="module" class="form-select">
                        <?php foreach ($modules as $key => $item): ?>
                            <option value="<?= e($key) ?>" <?= $module === $key ? 'selected' : '' ?>><?= e($item['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-primary">Tampilkan</button></div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Preview <?= e($modules[$module]['title']) ?></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><?php if (!empty($rows)): ?><?php foreach (array_keys($rows[0]) as $key): ?><th><?= e((string) $key) ?></th><?php endforeach; ?><?php endif; ?></tr></thead>
                    <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $row): ?>
                                <tr><?php foreach ($row as $value): ?><td><?= e((string) $value) ?></td><?php endforeach; ?></tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td class="text-center text-muted py-4">Tidak ada data untuk laporan ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
