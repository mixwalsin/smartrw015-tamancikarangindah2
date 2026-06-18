<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('kartu-keluarga') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Detail Kartu Keluarga</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('kartu-keluarga/pindah/' . $kk['id']) ?>" class="btn btn-warning btn-sm">
                <i class="bi bi-geo-alt me-1"></i>Pindah KK
            </a>
            <a href="<?= url('kartu-keluarga/print/' . $kk['id']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                <i class="bi bi-printer me-1"></i>Cetak KK Internal
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Data KK</strong></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Nomor KK</dt><dd class="col-7"><code><?= e($kk['nomor_kk']) ?></code></dd>
                        <dt class="col-5">Kepala Keluarga</dt><dd class="col-7"><?= e($kk['kepala_keluarga']) ?></dd>
                        <dt class="col-5">Alamat</dt><dd class="col-7"><?= e($kk['alamat']) ?></dd>
                        <dt class="col-5">RT / RW</dt><dd class="col-7">RT <?= e($kk['rt']) ?> / RW <?= e($kk['rw']) ?></dd>
                        <dt class="col-5">Jumlah Anggota</dt><dd class="col-7"><?= (int) $kk['jumlah_anggota'] ?> orang</dd>
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white"><strong>Tambah Anggota Keluarga</strong></div>
                <div class="card-body">
                    <form method="POST" action="<?= url('kartu-keluarga/anggota/' . $kk['id']) ?>">
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <label class="form-label">Warga</label>
                            <select name="warga_id" class="form-select" required>
                                <option value="">-- Pilih Warga --</option>
                                <?php foreach ($wargaOptions as $warga): ?>
                                    <option value="<?= (int) $warga['id'] ?>">
                                        <?= e($warga['nik'] . ' - ' . $warga['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($wargaOptions)): ?>
                                <small class="text-muted">Data warga belum tersedia.</small>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hubungan</label>
                            <select name="hubungan" class="form-select" required>
                                <?php foreach (['Kepala Keluarga','Istri','Anak','Menantu','Cucu','Orang Tua','Mertua','Famili Lain','Pembantu','Lainnya'] as $hubungan): ?>
                                    <option value="<?= e($hubungan) ?>"><?= e($hubungan) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Anggota
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Anggota Keluarga</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Hubungan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($anggota)): ?>
                            <?php foreach ($anggota as $i => $item): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><code><?= e($item['nik'] ?? '-') ?></code></td>
                                    <td><?= e($item['nama'] ?? '-') ?></td>
                                    <td><?= e($item['hubungan']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada anggota keluarga.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white"><strong>Riwayat Perubahan</strong></div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($riwayat)): ?>
                        <?php foreach ($riwayat as $item): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong><?= e($item['aksi']) ?></strong>
                                    <small class="text-muted"><?= e($item['created_at']) ?></small>
                                </div>
                                <div class="small text-muted"><?= e($item['keterangan'] ?? '-') ?></div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">Belum ada riwayat perubahan.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
