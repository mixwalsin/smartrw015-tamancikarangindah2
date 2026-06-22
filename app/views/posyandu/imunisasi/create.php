<!-- Tambah Imunisasi -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('posyandu/imunisasi') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Tambah Data Imunisasi</h4>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('posyandu/imunisasi/store') ?>" method="POST">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Balita <span class="text-danger">*</span></label>
                                <select name="balita_id" class="form-select" required>
                                    <option value="">-- Pilih Balita --</option>
                                    <?php foreach ($balitas as $b): ?>
                                        <option value="<?= $b['id'] ?>"
                                            <?= ((int)($_POST['balita_id'] ?? $selectedBalitaId ?? 0)) === (int)$b['id'] ? 'selected' : '' ?>>
                                            <?= e($b['nama']) ?> (RT <?= e($b['rt']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Jenis Imunisasi <span class="text-danger">*</span></label>
                                <select name="jenis_imunisasi" class="form-select" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <?php
                                    $listImunisasi = ['HB-0','BCG','DPT-HB-Hib 1','DPT-HB-Hib 2','DPT-HB-Hib 3',
                                                      'Polio 1','Polio 2','Polio 3','Polio 4','IPV','Campak/MR',
                                                      'Vitamin A','Lainnya'];
                                    foreach ($listImunisasi as $jenis):
                                    ?>
                                        <option value="<?= $jenis ?>"
                                            <?= ($_POST['jenis_imunisasi'] ?? '') === $jenis ? 'selected' : '' ?>>
                                            <?= $jenis ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal</label>
                                <input type="date" name="tanggal_imunisasi" class="form-control"
                                       value="<?= e($_POST['tanggal_imunisasi'] ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tempat Imunisasi</label>
                                <input type="text" name="tempat_imunisasi" class="form-control"
                                       placeholder="Posyandu, Puskesmas, dll."
                                       value="<?= e($_POST['tempat_imunisasi'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="2"><?= e($_POST['catatan'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-info text-white"><i class="bi bi-save me-1"></i>Simpan</button>
                            <a href="<?= url('posyandu/imunisasi') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
