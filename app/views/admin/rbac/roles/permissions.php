<!-- Admin: Kelola Permission Role -->
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="<?= url('admin/roles') ?>" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-key me-2 text-info"></i>Permission: <span class="text-primary"><?= e($role['name']) ?></span>
            </h4>
            <small class="text-muted">Centang permission yang dimiliki role ini</small>
        </div>
    </div>

    <form method="POST" action="<?= url('admin/roles/sync-permissions/' . $role['id']) ?>">
        <?= csrfField() ?>

        <div class="row g-3 mb-3">
            <?php foreach ($allPermissions as $modul => $perms): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <span class="fw-semibold text-capitalize">
                                <i class="bi bi-grid-3x3-gap me-1"></i><?= e($modul) ?>
                            </span>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-1 check-all"
                                    data-modul="<?= e($modul) ?>">
                                Pilih Semua
                            </button>
                        </div>
                        <div class="card-body py-2">
                            <?php foreach ($perms as $perm): ?>
                                <div class="form-check">
                                    <input class="form-check-input perm-check-<?= e($modul) ?>"
                                           type="checkbox"
                                           name="permissions[]"
                                           value="<?= (int) $perm['id'] ?>"
                                           id="perm_<?= (int) $perm['id'] ?>"
                                           <?= in_array((int) $perm['id'], $rolePermissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="perm_<?= (int) $perm['id'] ?>">
                                        <span class="fw-medium"><?= e($perm['name']) ?></span><br>
                                        <code class="text-muted" style="font-size:.75em"><?= e($perm['slug']) ?></code>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-info text-white">
                <i class="bi bi-save me-1"></i>Simpan Permission
            </button>
            <a href="<?= url('admin/roles') ?>" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </form>

</div>

<script>
document.querySelectorAll('.check-all').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const modul    = this.dataset.modul;
        const checks   = document.querySelectorAll('.perm-check-' + modul);
        const allCheck = Array.from(checks).every(c => c.checked);
        checks.forEach(c => c.checked = !allCheck);
        this.textContent = allCheck ? 'Pilih Semua' : 'Hapus Semua';
    });
});
</script>
