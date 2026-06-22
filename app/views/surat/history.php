<!-- Riwayat & Audit Surat -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Surat</h4>
        <a href="<?= url('surat') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?= url('surat/history') ?>" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari nama, NIK..."
                           value="<?= e($keyword ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <?php foreach (['draft','menunggu_rt','menunggu_rw','disetujui','ditolak','selesai'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($filterStatus ?? '') === $s ? 'selected' : '' ?>>
                                <?= \PengajuanSuratModel::statusLabel($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                    <a href="<?= url('surat/history') ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>No. Surat</th>
                            <th>Jenis</th>
                            <th>Pemohon</th>
                            <th>NIK</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagination['data'])): ?>
                            <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            <?php foreach ($pagination['data'] as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><small><?= $row['no_surat'] ? e($row['no_surat']) : '<span class="text-muted">-</span>' ?></small></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= e($row['jenis_kode']) ?></span>
                                    </td>
                                    <td><?= e($row['pemohon_nama']) ?></td>
                                    <td><code class="small"><?= e($row['pemohon_nik']) ?></code></td>
                                    <td>
                                        <span class="badge bg-<?= \PengajuanSuratModel::statusBadge($row['status']) ?>">
                                            <?= \PengajuanSuratModel::statusLabel($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted"><?= e(date('d/m/Y H:i', strtotime($row['created_at']))) ?></td>
                                    <td>
                                        <a href="<?= url('surat/show/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (in_array($row['status'], ['disetujui', 'selesai'])): ?>
                                            <a href="<?= url('surat/print/' . $row['id']) ?>" class="btn btn-sm btn-outline-success" target="_blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data riwayat surat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">Total: <?= number_format($pagination['total']) ?> surat</small>
            <?= paginate($pagination, 'surat/history') ?>
        </div>
    </div>

</div>
