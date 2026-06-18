<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('kartu-keluarga/show/' . $kk['id']) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">Pindah Kartu Keluarga</h4>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?= url('kartu-keluarga/pindah/' . $kk['id']) ?>">
                        <?= csrfField() ?>
                        <div class="alert alert-info">
                            KK: <code><?= e($kk['nomor_kk']) ?></code> - <?= e($kk['kepala_keluarga']) ?>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Baru</label>
                                <textarea name="alamat" rows="3" class="form-control" required><?= e($kk['alamat']) ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">RT Baru</label>
                                <input type="text" name="rt" maxlength="3" class="form-control" required value="<?= e($kk['rt']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">RW Baru</label>
                                <input type="text" name="rw" maxlength="3" class="form-control" required value="<?= e($kk['rw']) ?>">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                            </button>
                            <a href="<?= url('kartu-keluarga/show/' . $kk['id']) ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
