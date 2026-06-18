<!-- Admin: Edit Role -->
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="<?= url('admin/roles') ?>" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Role: <?= e($role['name']) ?></h4>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= url('admin/roles/update/' . $role['id']) ?>">
                        <?= csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= e($_POST['name'] ?? $role['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control"
                                   value="<?= e($_POST['slug'] ?? $role['slug']) ?>"
                                   <?= (int)$role['id'] === 1 ? 'readonly' : '' ?> required>
                            <?php if ((int)$role['id'] === 1): ?>
                                <div class="form-text text-warning">Slug Super Admin tidak dapat diubah.</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"><?= e($_POST['description'] ?? $role['description'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-lg me-1"></i>Perbarui
                            </button>
                            <a href="<?= url('admin/roles') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
