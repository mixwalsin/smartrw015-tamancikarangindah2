<!-- Pengajuan Surat Baru -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('surat') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0"><i class="bi bi-envelope-plus me-2 text-primary"></i>Ajukan Surat</h4>
            </div>

            <?php if (empty($jenis)): ?>
            <!-- Pilih Jenis Surat -->
            <div class="row g-3">
                <?php
                $jenisBadge = [
                    'DOMISILI'  => ['icon' => 'bi-house-door',     'color' => 'primary'],
                    'SKCK'      => ['icon' => 'bi-shield-check',   'color' => 'danger'],
                    'USAHA'     => ['icon' => 'bi-shop',           'color' => 'success'],
                    'TDK_MAMPU' => ['icon' => 'bi-heart',          'color' => 'warning'],
                    'KELAHIRAN' => ['icon' => 'bi-baby',           'color' => 'info'],
                    'KEMATIAN'  => ['icon' => 'bi-flower1',        'color' => 'secondary'],
                ];
                ?>
                <?php foreach ($jenisList as $j): ?>
                    <?php $info = $jenisBadge[$j['kode']] ?? ['icon' => 'bi-file-earmark-text', 'color' => 'primary']; ?>
                    <div class="col-md-4">
                        <a href="<?= url('surat/create?jenis=' . $j['id']) ?>" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center p-3 hover-card">
                                <div class="card-body">
                                    <i class="bi <?= $info['icon'] ?> fs-1 text-<?= $info['color'] ?> mb-3 d-block"></i>
                                    <h6 class="fw-bold"><?= e($j['nama']) ?></h6>
                                    <?php if ($j['deskripsi']): ?>
                                        <p class="small text-muted mb-0"><?= e(truncate($j['deskripsi'], 80)) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <span class="btn btn-<?= $info['color'] ?> btn-sm w-100">
                                        <i class="bi bi-arrow-right me-1"></i>Pilih
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
            <!-- Form Pengajuan -->
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <i class="bi bi-file-earmark-text me-1"></i>
                            Form Pengajuan: <strong><?= e($jenis['nama']) ?></strong>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?= url('surat/store') ?>" method="POST" enctype="multipart/form-data">
                                <?= csrfField() ?>
                                <input type="hidden" name="jenis_id" value="<?= (int) $jenis['id'] ?>">

                                <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Data Pemohon</h6>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="pemohon_nama" class="form-control" required
                                               placeholder="Sesuai KTP"
                                               value="<?= e($_POST['pemohon_nama'] ?? authUser()['name'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                        <input type="text" name="pemohon_nik" class="form-control" maxlength="16"
                                               placeholder="16 digit" pattern="\d{16}" required
                                               value="<?= e($_POST['pemohon_nik'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tempat Lahir</label>
                                        <input type="text" name="pemohon_tempat_lahir" class="form-control"
                                               value="<?= e($_POST['pemohon_tempat_lahir'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tanggal Lahir</label>
                                        <input type="date" name="pemohon_tgl_lahir" class="form-control"
                                               value="<?= e($_POST['pemohon_tgl_lahir'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                                        <select name="pemohon_jk" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            <option value="L" <?= ($_POST['pemohon_jk'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="P" <?= ($_POST['pemohon_jk'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">RT <span class="text-danger">*</span></label>
                                        <select name="pemohon_rt" class="form-select" required>
                                            <?php for ($i = 1; $i <= 7; $i++): ?>
                                                <?php $val = str_pad($i, 3, '0', STR_PAD_LEFT); ?>
                                                <option value="<?= $val ?>"
                                                    <?= ($_POST['pemohon_rt'] ?? '001') === $val ? 'selected' : '' ?>>
                                                    RT <?= $val ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Agama</label>
                                        <select name="pemohon_agama" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            <?php foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag): ?>
                                                <option value="<?= $ag ?>" <?= ($_POST['pemohon_agama'] ?? '') === $ag ? 'selected' : '' ?>><?= $ag ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Pekerjaan</label>
                                        <input type="text" name="pemohon_pekerjaan" class="form-control"
                                               value="<?= e($_POST['pemohon_pekerjaan'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                                        <input type="text" name="pemohon_no_hp" class="form-control"
                                               placeholder="08xxxxxxxxxx"
                                               value="<?= e($_POST['pemohon_no_hp'] ?? '') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                                        <textarea name="pemohon_alamat" class="form-control" rows="2" required
                                                  placeholder="Jl. ..., No. ..., RW015"><?= e($_POST['pemohon_alamat'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-muted mt-4 mb-3 border-bottom pb-2">Keterangan Surat</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Keperluan <span class="text-danger">*</span></label>
                                        <textarea name="keperluan" class="form-control" rows="3" required
                                                  placeholder="Jelaskan keperluan pengajuan surat ini..."><?= e($_POST['keperluan'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Keterangan Tambahan</label>
                                        <textarea name="keterangan_tambahan" class="form-control" rows="2"
                                                  placeholder="Keterangan tambahan (opsional)"><?= e($_POST['keterangan_tambahan'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Lampiran Dokumen</label>
                                        <input type="file" name="lampiran" class="form-control"
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">Format: PDF, JPG, PNG. Maks 5 MB.</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-1"></i>Kirim Pengajuan
                                    </button>
                                    <a href="<?= url('surat') ?>" class="btn btn-outline-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Syarat -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-header bg-warning text-dark">
                            <i class="bi bi-list-check me-1"></i>Persyaratan
                        </div>
                        <div class="card-body">
                            <?php if (!empty($jenis['syarat'])): ?>
                                <ul class="mb-0 ps-3">
                                    <?php foreach (explode("\n", $jenis['syarat']) as $syarat): ?>
                                        <?php $s = trim($syarat); if ($s !== ''): ?>
                                            <li class="mb-1"><?= e($s) ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted mb-0">Tidak ada persyaratan khusus.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-info text-white">
                            <i class="bi bi-info-circle me-1"></i>Alur Proses
                        </div>
                        <div class="card-body p-3">
                            <ol class="list-unstyled mb-0">
                                <?php
                                $steps = [
                                    ['icon' => 'bi-person', 'color' => 'primary', 'text' => 'Warga Submit'],
                                    ['icon' => 'bi-person-badge', 'color' => 'warning', 'text' => 'RT Verifikasi'],
                                    ['icon' => 'bi-building', 'color' => 'info', 'text' => 'RW Approval'],
                                    ['icon' => 'bi-file-pdf', 'color' => 'success', 'text' => 'Generate Surat'],
                                    ['icon' => 'bi-qr-code', 'color' => 'dark', 'text' => 'QR Verifikasi'],
                                ];
                                foreach ($steps as $i => $step): ?>
                                    <li class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-<?= $step['color'] ?> rounded-circle p-2">
                                            <i class="bi <?= $step['icon'] ?>"></i>
                                        </span>
                                        <span class="small"><?= $step['text'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.hover-card { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
.hover-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important; }
</style>
