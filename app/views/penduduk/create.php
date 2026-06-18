<!-- Tambah Penduduk -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('penduduk') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">Tambah Data Penduduk</h4>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="<?= url('penduduk/store') ?>" method="POST">
                        <?= csrfField() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nik" class="form-control" maxlength="16"
                                       placeholder="16 digit NIK" required
                                       value="<?= e($_POST['nik'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. KK</label>
                                <input type="text" name="no_kk" class="form-control" maxlength="16"
                                       placeholder="16 digit No. KK"
                                       value="<?= e($_POST['no_kk'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required
                                       placeholder="Nama sesuai KTP"
                                       value="<?= e($_POST['nama'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control"
                                       value="<?= e($_POST['tempat_lahir'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control"
                                       value="<?= e($_POST['tanggal_lahir'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" <?= ($_POST['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= ($_POST['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">RT</label>
                                <select name="rt" class="form-select">
                                    <option value="">-- Pilih RT --</option>
                                    <?php for ($i = 1; $i <= 15; $i++): ?>
                                        <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>"
                                            <?= ($_POST['rt'] ?? '') === str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                            RT <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status Kawin</label>
                                <select name="status_kawin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Belum Kawin">Belum Kawin</option>
                                    <option value="Kawin">Kawin</option>
                                    <option value="Cerai Hidup">Cerai Hidup</option>
                                    <option value="Cerai Mati">Cerai Mati</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Agama</label>
                                <select name="agama" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag): ?>
                                        <option value="<?= $ag ?>" <?= ($_POST['agama'] ?? '') === $ag ? 'selected' : '' ?>><?= $ag ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control"
                                       value="<?= e($_POST['pekerjaan'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2"><?= e($_POST['alamat'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                            <a href="<?= url('penduduk') ?>" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
