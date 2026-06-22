<!-- Catat Timbangan -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('posyandu/timbangan') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Catat Penimbangan Balita</h4>
            </div>

            <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle-fill"></i>
                Status gizi akan dihitung otomatis berdasarkan BB/U standar WHO.
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('posyandu/timbangan/store') ?>" method="POST">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Balita <span class="text-danger">*</span></label>
                                <select name="balita_id" class="form-select" required>
                                    <option value="">-- Pilih Balita --</option>
                                    <?php foreach ($balitas as $b): ?>
                                        <?php $umurBulan = $b['umur_bulan'] ?? 0; ?>
                                        <option value="<?= $b['id'] ?>"
                                            <?= ((int)($_POST['balita_id'] ?? $selectedBalitaId ?? 0)) === (int)$b['id'] ? 'selected' : '' ?>>
                                            <?= e($b['nama']) ?> (<?= $umurBulan ?> bln, RT <?= e($b['rt']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Timbang</label>
                                <input type="date" name="tanggal_timbang" class="form-control"
                                       value="<?= e($_POST['tanggal_timbang'] ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Berat Badan (kg) <span class="text-danger">*</span></label>
                                <input type="number" name="berat_badan" class="form-control" step="0.1" min="0.1" required
                                       value="<?= e($_POST['berat_badan'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tinggi Badan (cm)</label>
                                <input type="number" name="tinggi_badan" class="form-control" step="0.1" min="0"
                                       value="<?= e($_POST['tinggi_badan'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="2"><?= e($_POST['catatan'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Simpan</button>
                            <a href="<?= url('posyandu/timbangan') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
