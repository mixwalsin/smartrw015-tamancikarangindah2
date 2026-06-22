<?php
$role = strtolower((string) (authUser()['role'] ?? 'warga'));
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
            <h4 class="fw-bold mb-1"><i class="bi bi-megaphone-fill me-2 text-primary"></i>Sistem Pengaduan Warga</h4>
            <p class="text-muted mb-0">
                <?php if (in_array($role, ['rt', 'ketua_rt', 'admin_rt'], true)): ?>
                    Dashboard verifikasi RT, disposisi, dan monitoring tindak lanjut.
                <?php elseif (in_array($role, ['rw', 'ketua_rw', 'sekretaris_rw', 'bendahara_rw', 'admin', 'super_admin'], true)): ?>
                    Dashboard pengawasan RW, alokasi tindak lanjut, dan analytics pengaduan.
                <?php else: ?>
                    Laporkan masalah lingkungan, unggah foto, dan lacak status penanganan secara real-time.
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('pengaduan/report') ?>" class="btn btn-outline-success"><i class="bi bi-bar-chart me-1"></i>Analytics</a>
            <a href="<?= url('pengaduan/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Buat Pengaduan</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Total</small>
                    <div class="fs-3 fw-bold"><?= number_format($summary['total'] ?? 0) ?></div>
                    <div class="text-muted small">Seluruh pengaduan tercatat</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Diproses</small>
                    <div class="fs-3 fw-bold text-warning"><?= number_format($summary['diproses'] ?? 0) ?></div>
                    <div class="text-muted small">Menunggu tindak lanjut RT/RW</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Selesai</small>
                    <div class="fs-3 fw-bold text-success"><?= number_format($summary['selesai'] ?? 0) ?></div>
                    <div class="text-muted small">Pengaduan sudah ditangani</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Ditolak</small>
                    <div class="fs-3 fw-bold text-danger"><?= number_format($summary['ditolak'] ?? 0) ?></div>
                    <div class="text-muted small">Disertai alasan penolakan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-9">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="<?= url('pengaduan') ?>" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Kata kunci</label>
                            <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword'] ?? '') ?>" placeholder="No tiket, judul, deskripsi">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua</option>
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?= e($value) ?>" <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Kategori</label>
                            <select name="kategori_id" class="form-select">
                                <option value="">Semua</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>" <?= (string) ($filters['kategori_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Prioritas</label>
                            <select name="prioritas" class="form-select">
                                <option value="">Semua</option>
                                <?php foreach ($priorities as $value => $label): ?>
                                    <option value="<?= e($value) ?>" <?= ($filters['prioritas'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small text-muted">Dari</label>
                            <input type="date" name="date_from" class="form-control" value="<?= e($filters['date_from'] ?? '') ?>">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small text-muted">Sampai</label>
                            <input type="date" name="date_to" class="form-control" value="<?= e($filters['date_to'] ?? '') ?>">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Daftar Pengaduan</span>
                    <a href="<?= url('pengaduan/export/excel') ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i>Export Excel</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No Tiket</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th>Foto</th>
                                    <th>Komentar</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pagination['data'])): ?>
                                    <?php foreach ($pagination['data'] as $row): ?>
                                        <tr>
                                            <td><code><?= e($row['no_tiket'] ?? '-') ?></code></td>
                                            <td>
                                                <div class="fw-semibold"><?= e($row['judul']) ?></div>
                                                <div class="text-muted small"><?= e(truncate($row['deskripsi'], 70)) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge text-bg-light border"><?= e($row['kategori_nama'] ?? 'Tanpa Kategori') ?></span>
                                            </td>
                                            <td><span class="badge text-bg-dark"><?= e(ucfirst($row['prioritas'] ?? 'sedang')) ?></span></td>
                                            <td><span class="badge bg-<?= $badgeMap[$row['status']] ?? 'secondary' ?>"><?= e($statuses[$row['status']] ?? $row['status']) ?></span></td>
                                            <td><?= (int) ($row['total_foto'] ?? 0) ?></td>
                                            <td><?= (int) ($row['total_komentar'] ?? 0) ?></td>
                                            <td><?= e(formatDate($row['created_at'], 'd M Y H:i')) ?></td>
                                            <td>
                                                <a href="<?= url('pengaduan/show/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada pengaduan yang sesuai filter.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">Total <?= number_format($pagination['total'] ?? 0) ?> pengaduan</small>
                    <?= paginate($pagination, 'pengaduan') ?>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-bell me-2 text-warning"></i>Notifikasi Pengaduan</div>
                <div class="card-body p-0">
                    <?php if (!empty($notifications)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($notifications as $notif): ?>
                                <li class="list-group-item">
                                    <div class="fw-semibold small"><?= e($notif['judul']) ?></div>
                                    <div class="small text-muted"><?= e($notif['pesan']) ?></div>
                                    <div class="small text-muted mt-1"><?= e(formatDate($notif['created_at'], 'd M Y H:i')) ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="p-3 text-muted small">Belum ada notifikasi pengaduan.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Kategori Default</div>
                <div class="card-body">
                    <?php foreach ($categories as $category): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span><?= e($category['name']) ?></span>
                            <span class="badge rounded-pill" style="background: <?= e($category['warna'] ?? '#6c757d') ?>;">&nbsp;</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
