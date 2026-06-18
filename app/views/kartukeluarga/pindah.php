<!-- Pindah KK -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('kartukeluarga/show/' . $kk['id']) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0">Pindah Anggota KK</h4>
                    <small class="text-muted">KK <?= e($kk['no_kk']) ?> &mdash; RT <?= e($kk['rt_text'] ?? '—') ?></small>
                </div>
            </div>

            <?php if (empty($anggota)): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    KK ini belum memiliki anggota.
                </div>
            <?php else: ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('kartukeluarga/proses-pindah/' . $kk['id']) ?>" method="POST"
                          onsubmit="return confirm('Yakin ingin memindahkan anggota ini ke KK yang dipilih?')">
                        <?= csrfField() ?>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Pilih Anggota yang Pindah <span class="text-danger">*</span>
                                </label>
                                <select name="warga_id" class="form-select" required>
                                    <option value="">-- Pilih Anggota --</option>
                                    <?php foreach ($anggota as $a): ?>
                                        <option value="<?= $a['id'] ?>"
                                            <?= ($_POST['warga_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                                            <?= e($a['nama']) ?> &mdash; <?= e($a['hubungan']) ?>
                                            (NIK: <?= e($a['nik']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    KK Tujuan <span class="text-danger">*</span>
                                </label>
                                <select name="new_kk_id" class="form-select" required>
                                    <option value="">-- Pilih KK Tujuan --</option>
                                    <?php foreach ($allKk as $k): ?>
                                        <?php if ($k['id'] == $kk['id']) continue; ?>
                                        <option value="<?= $k['id'] ?>"
                                            <?= ($_POST['new_kk_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                                            <?= e($k['no_kk']) ?> &mdash; RT <?= e($k['rt_text']) ?>
                                            (<?= e($k['kepala_keluarga']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Pilih KK tujuan tempat anggota akan bergabung.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Hubungan di KK Tujuan <span class="text-danger">*</span>
                                </label>
                                <select name="hubungan" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <?php foreach (['Kepala Keluarga','Istri','Anak','Menantu','Cucu',
                                                    'Orang Tua','Mertua','Famili Lain','Pembantu','Lainnya'] as $h): ?>
                                        <option value="<?= $h ?>"
                                            <?= ($_POST['hubungan'] ?? '') === $h ? 'selected' : '' ?>>
                                            <?= $h ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 small">
                            <i class="bi bi-info-circle me-1"></i>
                            Anggota yang dipindahkan akan dikeluarkan dari KK ini dan dimasukkan ke KK tujuan.
                            Perubahan ini akan tercatat dalam riwayat kedua KK.
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-arrow-left-right me-1"></i>Pindahkan
                            </button>
                            <a href="<?= url('kartukeluarga/show/' . $kk['id']) ?>"
                               class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

            <?php endif; ?>

        </div>
    </div>
</div>
