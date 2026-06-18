<!-- Tambah Anggota KK -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('kartukeluarga/show/' . $kk['id']) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0">Tambah Anggota Keluarga</h4>
                    <small class="text-muted">KK <?= e($kk['no_kk']) ?> &mdash; <?= e($kk['kepala_keluarga'] ?? '—') ?></small>
                </div>
            </div>

            <!-- Lookup NIK -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-search me-1"></i>Cari Warga Terdaftar (opsional)
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" id="lookupNik" class="form-control"
                               placeholder="Masukkan NIK warga yang sudah terdaftar...">
                        <button class="btn btn-outline-primary" onclick="lookupWarga()">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                    <div id="lookupResult" class="mt-2"></div>
                    <div class="form-text">Jika warga sudah terdaftar di sistem, cari terlebih dahulu untuk mengisi otomatis.</div>
                </div>
            </div>

            <!-- Form Anggota -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-person-plus me-1 text-success"></i>Data Anggota
                </div>
                <div class="card-body p-4">
                    <form action="<?= url('kartukeluarga/store-anggota/' . $kk['id']) ?>" method="POST">
                        <?= csrfField() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                <input type="text" id="fieldNik" name="nik" class="form-control"
                                       maxlength="16" placeholder="16 digit NIK" required
                                       value="<?= e($_POST['nik'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Hubungan dalam KK <span class="text-danger">*</span></label>
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
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="fieldNama" name="nama" class="form-control"
                                       placeholder="Nama sesuai KTP" required
                                       value="<?= e($_POST['nama'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tempat Lahir</label>
                                <input type="text" id="fieldTempatLahir" name="tempat_lahir" class="form-control"
                                       value="<?= e($_POST['tempat_lahir'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date" id="fieldTanggalLahir" name="tanggal_lahir" class="form-control"
                                       value="<?= e($_POST['tanggal_lahir'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select id="fieldJK" name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" <?= ($_POST['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= ($_POST['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status Kawin</label>
                                <select id="fieldStatusKawin" name="status_kawin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach (['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'] as $sk): ?>
                                        <option value="<?= $sk ?>"
                                            <?= ($_POST['status_kawin'] ?? '') === $sk ? 'selected' : '' ?>>
                                            <?= $sk ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Agama</label>
                                <select id="fieldAgama" name="agama" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag): ?>
                                        <option value="<?= $ag ?>"
                                            <?= ($_POST['agama'] ?? '') === $ag ? 'selected' : '' ?>>
                                            <?= $ag ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pendidikan</label>
                                <select id="fieldPendidikan" name="pendidikan" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach (['Tidak Sekolah','SD','SMP','SMA/SMK','D1','D2','D3','S1','S2','S3'] as $p): ?>
                                        <option value="<?= $p ?>"
                                            <?= ($_POST['pendidikan'] ?? '') === $p ? 'selected' : '' ?>>
                                            <?= $p ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pekerjaan</label>
                                <input type="text" id="fieldPekerjaan" name="pekerjaan" class="form-control"
                                       value="<?= e($_POST['pekerjaan'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-person-plus me-1"></i>Tambah Anggota
                            </button>
                            <a href="<?= url('kartukeluarga/show/' . $kk['id']) ?>"
                               class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function lookupWarga() {
    const nik = document.getElementById('lookupNik').value.trim();
    if (!nik) return;

    const resultDiv = document.getElementById('lookupResult');
    resultDiv.innerHTML = '<span class="text-muted small">Mencari...</span>';

    fetch('<?= url('api/warga-lookup') ?>?nik=' + encodeURIComponent(nik))
        .then(r => r.json())
        .then(data => {
            if (data && data.id) {
                document.getElementById('fieldNik').value          = data.nik || '';
                document.getElementById('fieldNama').value         = data.nama || '';
                document.getElementById('fieldTempatLahir').value  = data.tempat_lahir || '';
                document.getElementById('fieldTanggalLahir').value = data.tanggal_lahir || '';
                document.getElementById('fieldPekerjaan').value    = data.pekerjaan || '';
                setSelect('fieldJK',          data.jenis_kelamin || '');
                setSelect('fieldStatusKawin', data.status_kawin  || '');
                setSelect('fieldAgama',       data.agama         || '');
                setSelect('fieldPendidikan',  data.pendidikan    || '');
                resultDiv.innerHTML = '<span class="text-success small"><i class="bi bi-check-circle me-1"></i>Data warga ditemukan dan diisi otomatis.</span>';
            } else {
                resultDiv.innerHTML = '<span class="text-warning small"><i class="bi bi-exclamation-circle me-1"></i>NIK tidak ditemukan. Isi data secara manual.</span>';
                document.getElementById('fieldNik').value = nik;
            }
        })
        .catch(() => {
            resultDiv.innerHTML = '<span class="text-warning small">Tidak dapat mencari. Isi data secara manual.</span>';
            document.getElementById('fieldNik').value = nik;
        });
}

function setSelect(id, value) {
    const sel = document.getElementById(id);
    if (!sel) return;
    for (let opt of sel.options) {
        if (opt.value === value) { sel.value = value; return; }
    }
}
</script>
