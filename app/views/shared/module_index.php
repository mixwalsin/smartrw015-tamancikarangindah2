<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi <?= e($icon ?? 'bi-grid') ?> me-2 text-primary"></i><?= e($title ?? 'Modul') ?></h4>
            <?php if (!empty($subtitle)): ?><div class="text-muted small"><?= e($subtitle) ?></div><?php endif; ?>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach (($extraButtons ?? []) as $button): ?>
                <a href="<?= e($button['url']) ?>" class="btn <?= e($button['class'] ?? 'btn-outline-primary') ?> btn-sm"><?= e($button['label']) ?></a>
            <?php endforeach; ?>
            <?php if (!empty($canCreate)): ?>
                <a href="<?= url($routeBase . '/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?= url($routeBase) ?>" class="d-flex gap-2">
                <?php if (($routeBase ?? '') === 'keuangan' && isset($_GET['tab'])): ?>
                    <input type="hidden" name="tab" value="<?= e((string) $_GET['tab']) ?>">
                <?php endif; ?>
                <input type="text" name="keyword" class="form-control" placeholder="Cari data..." value="<?= e($pagination['keyword'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                <?php if (!empty($pagination['keyword'])): ?>
                    <a href="<?= url($routeBase) ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th width="60">#</th>
                        <?php foreach ($columns as $column): ?>
                            <th><?= e($column['label']) ?></th>
                        <?php endforeach; ?>
                        <?php if (!empty($canShow) || !empty($canEdit) || !empty($canDelete)): ?><th width="170">Aksi</th><?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($pagination['data'])): ?>
                        <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                        <?php foreach ($pagination['data'] as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <?php foreach ($columns as $column): ?>
                                    <?php $value = $row[$column['key']] ?? '-'; ?>
                                    <td><?= e(is_scalar($value) ? (string) $value : json_encode($value)) ?></td>
                                <?php endforeach; ?>
                                <?php if (!empty($canShow) || !empty($canEdit) || !empty($canDelete)): ?>
                                    <td>
                                        <?php if (!empty($canShow)): ?>
                                            <a href="<?= url($routeBase . '/show/' . $row['id']) ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($canEdit)): ?>
                                            <a href="<?= url($routeBase . '/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($canDelete)): ?>
                                            <form method="POST" action="<?= url($routeBase . '/delete/' . $row['id']) ?>" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                                <?= csrfField() ?>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="<?= count($columns) + 2 ?>" class="text-center text-muted py-4">Belum ada data.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">Total: <?= number_format((int) ($pagination['total'] ?? 0)) ?> data</small>
            <?= paginate($pagination, $routeBase) ?>
        </div>
    </div>
</div>
