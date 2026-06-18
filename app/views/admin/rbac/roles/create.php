<!-- Admin: Tambah Role -->
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4">
        <a href="<?= url('admin/roles') ?>" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Role</h4>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= url('admin/roles/store') ?>">
                        <?= csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Contoh: Ketua RW"
                                   value="<?= e($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" class="form-control"
                                   placeholder="Contoh: ketua_rw"
                                   value="<?= e($_POST['slug'] ?? '') ?>" required>
                            <div class="form-text">Gunakan huruf kecil, angka, dan underscore.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Deskripsi singkat role ini..."><?= e($_POST['description'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan
                            </button>
                            <a href="<?= url('admin/roles') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Auto-generate slug dari name
document.querySelector('[name="name"]')?.addEventListener('input', function () {
    const slug = document.getElementById('slug');
    if (!slug.dataset.manual) {
        slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
    }
});
document.getElementById('slug')?.addEventListener('input', function () {
    this.dataset.manual = '1';
});
</script>
