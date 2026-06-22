<!-- Tambah Balita -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('posyandu/balita') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Tambah Data Balita</h4>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('posyandu/balita/store') ?>" method="POST">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required
                                       value="<?= e($_POST['nama'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="L" <?= ($_POST['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= ($_POST['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_lahir" class="form-control" required
                                       value="<?= e($_POST['tgl_lahir'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">RT</label>
                                <select name="rt" class="form-select">
                                    <?php for ($i = 1; $i <= 15; $i++): ?>
                                        <?php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); ?>
                                        <option value="<?= $rtVal ?>"
                                            <?= ($_POST['rt'] ?? '007') === $rtVal ? 'selected' : '' ?>>
                                            RT <?= $rtVal ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Ibu <span class="text-danger">*</span></label>
                                <input type="text" name="nama_ibu" class="form-control" required
                                       value="<?= e($_POST['nama_ibu'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control"
                                       value="<?= e($_POST['nama_ayah'] ?? '') ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Alamat</label>
                                <input type="text" name="alamat" class="form-control"
                                       value="<?= e($_POST['alamat'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">No. Rumah</label>
                                <input type="text" name="no_rumah" class="form-control"
                                       value="<?= e($_POST['no_rumah'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Berat Badan (kg)</label>
                                <input type="number" name="berat_badan" class="form-control" step="0.1" min="0"
                                       value="<?= e($_POST['berat_badan'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tinggi Badan (cm)</label>
                                <input type="number" name="tinggi_badan" class="form-control" step="0.1" min="0"
                                       value="<?= e($_POST['tinggi_badan'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status Imunisasi</label>
                                <select name="status_imunisasi" class="form-select">
                                    <option value="belum" <?= ($_POST['status_imunisasi'] ?? 'belum') === 'belum' ? 'selected' : '' ?>>Belum</option>
                                    <option value="tidak_lengkap" <?= ($_POST['status_imunisasi'] ?? '') === 'tidak_lengkap' ? 'selected' : '' ?>>Belum Lengkap</option>
                                    <option value="lengkap" <?= ($_POST['status_imunisasi'] ?? '') === 'lengkap' ? 'selected' : '' ?>>Lengkap</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="2"><?= e($_POST['catatan'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                            <a href="<?= url('posyandu/balita') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
