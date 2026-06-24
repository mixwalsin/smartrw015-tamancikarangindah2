<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('kartu-keluarga') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">Tambah Kartu Keluarga</h4>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('kartu-keluarga/store') ?>" method="POST">
                        <?= csrfField() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nomor KK <span class="text-danger">*</span></label>
                                <input type="text" name="nomor_kk" maxlength="16" class="form-control" required
                                       value="<?= e($_POST['nomor_kk'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kepala Keluarga <span class="text-danger">*</span></label>
                                <input type="text" name="kepala_keluarga" class="form-control" required
                                       value="<?= e($_POST['kepala_keluarga'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">RT <span class="text-danger">*</span></label>
                                <input type="text" name="rt" maxlength="3" class="form-control" placeholder="001" required
                                       value="<?= e($_POST['rt'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">RW <span class="text-danger">*</span></label>
                                <input type="text" name="rw" maxlength="3" class="form-control" placeholder="015" required
                                       value="<?= e($_POST['rw'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="3" required><?= e($_POST['alamat'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Anggota (awal)</label>
                                <input type="number" name="jumlah_anggota" min="0" class="form-control"
                                       value="<?= e($_POST['jumlah_anggota'] ?? '0') ?>">
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                            <a href="<?= url('kartu-keluarga') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
