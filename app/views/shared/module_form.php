<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url($routeBase) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0"><i class="bi <?= e($icon ?? 'bi-pencil-square') ?> me-2 text-primary"></i><?= e($title ?? 'Form') ?></h4>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= e($actionUrl) ?>" method="POST">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <?php foreach ($fields as $name => $field): ?>
                                <?php $type = $field['type'] ?? 'text'; ?>
                                <div class="<?= in_array($type, ['textarea'], true) ? 'col-12' : 'col-md-6' ?>">
                                    <label class="form-label fw-semibold"><?= e($field['label'] ?? $name) ?><?php if (!empty($field['required'])): ?> <span class="text-danger">*</span><?php endif; ?></label>
                                    <?php if ($type === 'textarea'): ?>
                                        <textarea name="<?= e($name) ?>" class="form-control" rows="3" <?= !empty($field['required']) ? 'required' : '' ?> <?= !empty($field['readonly']) ? 'readonly' : '' ?>><?= e((string) ($field['value'] ?? '')) ?></textarea>
                                    <?php elseif ($type === 'select'): ?>
                                        <select name="<?= e($name) ?>" class="form-select" <?= !empty($field['required']) ? 'required' : '' ?> <?= !empty($field['readonly']) ? 'disabled' : '' ?>>
                                            <option value="">-- Pilih --</option>
                                            <?php foreach (($field['options'] ?? []) as $optionValue => $optionLabel): ?>
                                                <option value="<?= e((string) $optionValue) ?>" <?= (string) ($field['value'] ?? '') === (string) $optionValue ? 'selected' : '' ?>><?= e((string) $optionLabel) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (!empty($field['readonly'])): ?><input type="hidden" name="<?= e($name) ?>" value="<?= e((string) ($field['value'] ?? '')) ?>"><?php endif; ?>
                                    <?php else: ?>
                                        <input type="<?= e($type) ?>" name="<?= e($name) ?>" class="form-control" value="<?= e((string) ($field['value'] ?? '')) ?>" <?= !empty($field['required']) ? 'required' : '' ?> <?= !empty($field['readonly']) ? 'readonly' : '' ?>>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?= e($submitText ?? 'Simpan') ?></button>
                            <a href="<?= url($routeBase) ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
