<!-- Edit Ibu Hamil -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('posyandu/ibu-hamil') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Edit Data Ibu Hamil</h4>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('posyandu/ibu-hamil/update/' . $ibuHamil['id']) ?>" method="POST">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required
                                       value="<?= e($_POST['nama'] ?? $ibuHamil['nama']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Umur (tahun)</label>
                                <input type="number" name="umur" class="form-control" min="10" max="60"
                                       value="<?= e($_POST['umur'] ?? $ibuHamil['umur']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bulan Kehamilan</label>
                                <select name="bulan_kehamilan" class="form-select">
                                    <?php for ($i = 1; $i <= 9; $i++): ?>
                                        <option value="<?= $i ?>"
                                            <?= ($_POST['bulan_kehamilan'] ?? $ibuHamil['bulan_kehamilan']) == $i ? 'selected' : '' ?>>
                                            Bulan ke-<?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">RT</label>
                                <select name="rt" class="form-select">
                                    <?php for ($i = 1; $i <= 15; $i++): ?>
                                        <?php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); ?>
                                        <option value="<?= $rtVal ?>"
                                            <?= ($_POST['rt'] ?? $ibuHamil['rt']) === $rtVal ? 'selected' : '' ?>>
                                            RT <?= $rtVal ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Alamat</label>
                                <input type="text" name="alamat" class="form-control"
                                       value="<?= e($_POST['alamat'] ?? $ibuHamil['alamat'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">No. Rumah</label>
                                <input type="text" name="no_rumah" class="form-control"
                                       value="<?= e($_POST['no_rumah'] ?? $ibuHamil['no_rumah'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tgl Perkiraan Lahir</label>
                                <input type="date" name="tgl_perkiraan_lahir" class="form-control"
                                       value="<?= e($_POST['tgl_perkiraan_lahir'] ?? $ibuHamil['tgl_perkiraan_lahir'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status Kesehatan</label>
                                <select name="status_kesehatan" class="form-select">
                                    <option value="normal" <?= ($_POST['status_kesehatan'] ?? $ibuHamil['status_kesehatan']) === 'normal' ? 'selected' : '' ?>>Normal</option>
                                    <option value="berisiko_tinggi" <?= ($_POST['status_kesehatan'] ?? $ibuHamil['status_kesehatan']) === 'berisiko_tinggi' ? 'selected' : '' ?>>Berisiko Tinggi</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="2"><?= e($_POST['catatan'] ?? $ibuHamil['catatan'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Perbarui</button>
                            <a href="<?= url('posyandu/ibu-hamil') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
