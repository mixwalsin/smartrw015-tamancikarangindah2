<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url($routeBase) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0"><i class="bi <?= e($icon ?? 'bi-eye') ?> me-2 text-primary"></i><?= e($title ?? 'Detail') ?></h4>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <?php foreach ($columns as $column): ?>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1"><?= e($column['label']) ?></div>
                                <div class="fw-semibold"><?= nl2br(e((string) ($row[$column['key']] ?? '-'))) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($extraContent ?? '')): ?>
                        <?= $extraContent ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
