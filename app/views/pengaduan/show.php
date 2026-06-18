<?php
$role = strtolower((string) (authUser()['role'] ?? 'warga'));
$statusOrder = ['diterima', 'diproses_rt', 'diproses_rw', 'dalam_perbaikan', 'selesai'];
$currentIndex = array_search($pengaduan['status'], $statusOrder, true);
$badgeMap = [
    'diterima' => 'secondary',
    'diproses_rt' => 'info',
    'diproses_rw' => 'primary',
    'dalam_perbaikan' => 'warning text-dark',
    'selesai' => 'success',
    'ditolak' => 'danger',
];
?>
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="text-muted small">No. Tiket</div>
            <h4 class="fw-bold mb-1"><?= e($pengaduan['no_tiket'] ?? ('PGD-' . $pengaduan['id'])) ?></h4>
            <div class="text-muted"><?= e($pengaduan['judul']) ?></div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= url('pengaduan/export/pdf/' . $pengaduan['id']) ?>" class="btn btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            <a href="<?= url('pengaduan') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                        <div>
                            <span class="badge bg-<?= $badgeMap[$pengaduan['status']] ?? 'secondary' ?> mb-2"><?= e($statuses[$pengaduan['status']] ?? $pengaduan['status']) ?></span>
                            <div class="text-muted small">Pelapor: <?= e($pengaduan['pelapor_nama'] ?? '-') ?><?= !empty($pengaduan['rt_kode']) ? ' · RT ' . e($pengaduan['rt_kode']) : '' ?></div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Prioritas</div>
                            <div class="fw-semibold"><?= e(ucfirst($pengaduan['prioritas'] ?? 'sedang')) ?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small fw-semibold mb-2">Progress Status</div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($statuses as $value => $label): ?>
                                <?php $done = $currentIndex !== false && ($value !== 'ditolak') && array_search($value, $statusOrder, true) !== false && array_search($value, $statusOrder, true) <= $currentIndex; ?>
                                <span class="badge rounded-pill <?= $pengaduan['status'] === $value ? 'text-bg-primary' : ($done ? 'text-bg-success' : 'text-bg-light border text-dark') ?>">
                                    <?= e($label) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Kategori</div>
                            <div class="fw-semibold"><?= e($pengaduan['kategori_nama'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">SLA Target</div>
                            <div class="fw-semibold"><?= !empty($pengaduan['sla_target_at']) ? e(formatDate($pengaduan['sla_target_at'], 'd M Y H:i')) : '-' ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Lokasi</div>
                            <div class="fw-semibold"><?= e($pengaduan['lokasi'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Dibuat</div>
                            <div class="fw-semibold"><?= e(formatDate($pengaduan['created_at'], 'd M Y H:i')) ?></div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Deskripsi</div>
                            <div class="border rounded p-3 bg-light-subtle"><?= nl2br(e($pengaduan['deskripsi'])) ?></div>
                        </div>
                        <?php if (!empty($pengaduan['rejection_reason'])): ?>
                            <div class="col-12">
                                <div class="alert alert-danger mb-0"><strong>Alasan penolakan:</strong> <?= e($pengaduan['rejection_reason']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-images me-2 text-primary"></i>Galeri Foto</div>
                <div class="card-body">
                    <?php if (!empty($pengaduan['fotos'])): ?>
                        <div class="row g-3">
                            <?php foreach ($pengaduan['fotos'] as $foto): ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="border rounded overflow-hidden h-100">
                                        <img src="<?= url('storage/uploads/' . $foto['foto_path']) ?>" alt="Foto pengaduan" class="img-fluid w-100" style="height: 220px; object-fit: cover;">
                                        <div class="p-2 d-flex justify-content-between align-items-center gap-2">
                                            <a href="<?= url('pengaduan/foto/download/' . $foto['id']) ?>" class="btn btn-sm btn-outline-primary">Unduh</a>
                                            <?php if ((int) ($pengaduan['user_id'] ?? 0) === (int) (authUser()['id'] ?? 0) || in_array($role, ['admin', 'super_admin', 'rw', 'ketua_rw', 'rt', 'ketua_rt', 'admin_rt'], true)): ?>
                                                <form method="POST" action="<?= url('pengaduan/foto/delete/' . $foto['id']) ?>">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="pengaduan_id" value="<?= (int) $pengaduan['id'] ?>">
                                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus foto ini?')">Hapus</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">Belum ada foto terunggah.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-chat-dots me-2 text-success"></i>Timeline Komunikasi</div>
                <div class="card-body">
                    <div class="timeline mb-4">
                        <?php foreach ($pengaduan['status_history'] as $history): ?>
                            <div class="border-start border-3 ps-3 mb-3">
                                <div class="fw-semibold"><?= e($statuses[$history['status_baru']] ?? $history['status_baru']) ?></div>
                                <div class="small text-muted"><?= e($history['changed_by_name'] ?? 'Sistem') ?> · <?= e(formatDate($history['changed_at'], 'd M Y H:i')) ?></div>
                                <?php if (!empty($history['keterangan'])): ?><div class="small mt-1"><?= e($history['keterangan']) ?></div><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($pengaduan['komentars'])): ?>
                        <?php foreach ($pengaduan['komentars'] as $komentar): ?>
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-semibold"><?= e($komentar['nama_user'] ?? 'Pengguna') ?></div>
                                        <div class="small text-muted"><?= e(formatDate($komentar['created_at'], 'd M Y H:i')) ?></div>
                                    </div>
                                    <?php if ((int) ($komentar['user_id'] ?? 0) === (int) (authUser()['id'] ?? 0) || in_array($role, ['admin', 'super_admin', 'rw', 'ketua_rw', 'rt', 'ketua_rt', 'admin_rt'], true)): ?>
                                        <form action="<?= url('pengaduan/comment/delete/' . $komentar['id']) ?>" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="pengaduan_id" value="<?= (int) $pengaduan['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-2"><?= nl2br(e($komentar['komentar'])) ?></div>
                                <?php if (!empty($komentar['lampiran_path'])): ?>
                                    <a class="small d-inline-block mt-2" href="<?= url('storage/uploads/' . $komentar['lampiran_path']) ?>" target="_blank">Lihat lampiran</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form action="<?= url('pengaduan/comment/' . $pengaduan['id']) ?>" method="POST" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tambahkan komentar / follow-up</label>
                            <textarea name="komentar" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lampiran komentar</label>
                            <input type="file" name="lampiran" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                        </div>
                        <button class="btn btn-success"><i class="bi bi-send me-1"></i>Kirim Komentar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">Upload Foto Tambahan</div>
                <div class="card-body">
                    <form action="<?= url('pengaduan/foto/upload/' . $pengaduan['id']) ?>" method="POST" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <input type="file" name="foto[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif">
                        </div>
                        <button class="btn btn-outline-primary w-100">Unggah Foto</button>
                    </form>
                </div>
            </div>

            <?php if (in_array($role, ['admin', 'super_admin', 'rw', 'ketua_rw', 'sekretaris_rw', 'bendahara_rw', 'rt', 'ketua_rt', 'admin_rt'], true)): ?>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-semibold">Update Status</div>
                    <div class="card-body">
                        <form action="<?= url('pengaduan/update-status/' . $pengaduan['id']) ?>" method="POST">
                            <?= csrfField() ?>
                            <div class="mb-3">
                                <select name="status" class="form-select" required>
                                    <?php foreach ($statuses as $value => $label): ?>
                                        <option value="<?= e($value) ?>" <?= $pengaduan['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan update status / alasan penolakan"></textarea>
                            </div>
                            <button class="btn btn-primary w-100">Simpan Status</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array($role, ['rt', 'ketua_rt', 'admin_rt', 'admin', 'super_admin'], true)): ?>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-semibold">Disposisi RT</div>
                    <div class="card-body">
                        <form action="<?= url('pengaduan/disposisi-rt/' . $pengaduan['id']) ?>" method="POST">
                            <?= csrfField() ?>
                            <div class="mb-3">
                                <label class="form-label">Catatan RT</label>
                                <textarea name="catatan" class="form-control" rows="3" required><?= e($pengaduan['disposisi_rt']['catatan'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jadwal Penanganan</label>
                                <input type="datetime-local" name="jadwal_penanganan" class="form-control" value="<?= !empty($pengaduan['disposisi_rt']['jadwal_penanganan']) ? e(date('Y-m-d\TH:i', strtotime($pengaduan['disposisi_rt']['jadwal_penanganan']))) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Assign Petugas</label>
                                <input type="number" name="petugas_id" class="form-control" min="1" placeholder="ID petugas RT" value="<?= e((string) ($pengaduan['disposisi_rt']['petugas_id'] ?? '')) ?>">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="teruskan_ke_rw" value="1" id="teruskanKeRw">
                                <label class="form-check-label" for="teruskanKeRw">Teruskan ke RW</label>
                            </div>
                            <button class="btn btn-outline-info w-100">Simpan Disposisi RT</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array($role, ['rw', 'ketua_rw', 'sekretaris_rw', 'bendahara_rw', 'admin', 'super_admin'], true)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">Disposisi RW</div>
                    <div class="card-body">
                        <form action="<?= url('pengaduan/disposisi-rw/' . $pengaduan['id']) ?>" method="POST">
                            <?= csrfField() ?>
                            <div class="mb-3">
                                <label class="form-label">Catatan / Instruksi RW</label>
                                <textarea name="catatan" class="form-control" rows="3" required><?= e($pengaduan['disposisi_rw']['catatan'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keputusan</label>
                                <select name="keputusan" class="form-select">
                                    <option value="review">Review lebih lanjut</option>
                                    <option value="approve">Approve & lanjut perbaikan</option>
                                    <option value="reject">Reject pengaduan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alokasi Budget</label>
                                <input type="number" min="0" step="0.01" name="alokasi_budget" class="form-control" value="<?= e((string) ($pengaduan['disposisi_rw']['alokasi_budget'] ?? '')) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Departemen / Divisi</label>
                                <input type="text" name="departemen" class="form-control" value="<?= e((string) ($pengaduan['disposisi_rw']['departemen'] ?? '')) ?>" placeholder="Contoh: Infrastruktur Lingkungan">
                            </div>
                            <button class="btn btn-outline-primary w-100">Simpan Disposisi RW</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
