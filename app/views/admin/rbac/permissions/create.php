<!-- Admin: Tambah Permission -->
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="<?= url('admin/permissions') ?>" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>Tambah Permission</h4>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= url('admin/permissions/store') ?>">
                        <?= csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Permission <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Contoh: Lihat Warga"
                                   value="<?= e($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" class="form-control"
                                   placeholder="Contoh: warga.read"
                                   value="<?= e($_POST['slug'] ?? '') ?>" required>
                            <div class="form-text">Format: <code>modul.aksi</code> (huruf kecil, titik sebagai pemisah).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Modul <span class="text-danger">*</span></label>
                            <input type="text" name="modul" id="modul" class="form-control"
                                   list="modulList"
                                   placeholder="Contoh: warga"
                                   value="<?= e($_POST['modul'] ?? '') ?>" required>
                            <datalist id="modulList">
                                <?php foreach ($moduls as $m): ?>
                                    <option value="<?= e($m) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <input type="text" name="description" class="form-control"
                                   placeholder="Deskripsi singkat..."
                                   value="<?= e($_POST['description'] ?? '') ?>">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>Simpan
                            </button>
                            <a href="<?= url('admin/permissions') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Auto-fill modul dari slug
document.getElementById('slug')?.addEventListener('input', function () {
    const parts = this.value.split('.');
    if (parts.length >= 1) {
        const modulField = document.getElementById('modul');
        if (!modulField.dataset.manual) {
            modulField.value = parts[0];
        }
    }
});
document.getElementById('modul')?.addEventListener('input', function () {
    this.dataset.manual = '1';
});
</script>
