<!-- Daftar Pengajuan Surat -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-envelope-paper me-2 text-primary"></i>Surat Menyurat</h4>
        <a href="<?= url('surat/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Ajukan Surat
        </a>
    </div>

    <!-- Statistik Status -->
    <?php
    $statusList = [
        'draft'        => ['label' => 'Draft',        'icon' => 'bi-file-earmark',       'color' => 'secondary'],
        'menunggu_rt'  => ['label' => 'Menunggu RT',  'icon' => 'bi-hourglass-split',    'color' => 'warning'],
        'menunggu_rw'  => ['label' => 'Menunggu RW',  'icon' => 'bi-hourglass',          'color' => 'info'],
        'disetujui'    => ['label' => 'Disetujui',    'icon' => 'bi-check-circle',       'color' => 'success'],
        'ditolak'      => ['label' => 'Ditolak',      'icon' => 'bi-x-circle',           'color' => 'danger'],
        'selesai'      => ['label' => 'Selesai',      'icon' => 'bi-check-circle-fill',  'color' => 'primary'],
    ];
    ?>
    <div class="row g-2 mb-4">
        <?php foreach ($statusList as $key => $info): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= url('surat?status=' . $key) ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center py-2 <?= $filterStatus === $key ? 'border border-' . $info['color'] . ' border-2' : '' ?>">
                        <div class="card-body p-2">
                            <i class="bi <?= $info['icon'] ?> fs-4 text-<?= $info['color'] ?>"></i>
                            <div class="fw-bold fs-5"><?= $statusCounts[$key] ?? 0 ?></div>
                            <div class="small text-muted"><?= $info['label'] ?></div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?= url('surat') ?>" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari nama, NIK, atau no. surat..."
                           value="<?= e($keyword ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <?php foreach ($statusList as $key => $info): ?>
                            <option value="<?= $key ?>" <?= ($filterStatus ?? '') === $key ? 'selected' : '' ?>>
                                <?= $info['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
                    <?php if (($filterStatus ?? '') !== '' || ($keyword ?? '') !== ''): ?>
                        <a href="<?= url('surat') ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i> Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Pengajuan -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Jenis Surat</th>
                            <th>Pemohon</th>
                            <th>NIK</th>
                            <th>RT</th>
                            <th>Keperluan</th>
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
                                    <td>
                                        <span class="badge bg-secondary"><?= e($row['jenis_kode']) ?></span>
                                        <div class="small"><?= e($row['jenis_nama']) ?></div>
                                    </td>
                                    <td><?= e($row['pemohon_nama']) ?></td>
                                    <td><code class="small"><?= e($row['pemohon_nik']) ?></code></td>
                                    <td>RT <?= e($row['pemohon_rt']) ?></td>
                                    <td class="small"><?= e(truncate($row['keperluan'], 40)) ?></td>
                                    <td>
                                        <?php
                                        $badge = \PengajuanSuratModel::statusBadge($row['status']);
                                        $label = \PengajuanSuratModel::statusLabel($row['status']);
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                    </td>
                                    <td class="small text-muted"><?= e(date('d/m/Y', strtotime($row['created_at']))) ?></td>
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
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada pengajuan surat.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pagination)): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Total: <?= number_format($pagination['total']) ?> pengajuan
                </small>
                <?= paginate($pagination, 'surat') ?>
            </div>
        <?php endif; ?>
    </div>

</div>
