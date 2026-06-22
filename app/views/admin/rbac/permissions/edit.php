<!-- Admin: Edit Permission -->
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="<?= url('admin/permissions') ?>" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Permission: <code><?= e($perm['slug']) ?></code></h4>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= url('admin/permissions/update/' . $perm['id']) ?>">
                        <?= csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Permission <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= e($_POST['name'] ?? $perm['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control"
                                   value="<?= e($_POST['slug'] ?? $perm['slug']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Modul <span class="text-danger">*</span></label>
                            <input type="text" name="modul" class="form-control"
                                   list="modulList"
                                   value="<?= e($_POST['modul'] ?? $perm['modul']) ?>" required>
                            <datalist id="modulList">
                                <?php foreach ($moduls as $m): ?>
                                    <option value="<?= e($m) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <input type="text" name="description" class="form-control"
                                   value="<?= e($_POST['description'] ?? $perm['description'] ?? '') ?>">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-lg me-1"></i>Perbarui
                            </button>
                            <a href="<?= url('admin/permissions') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
