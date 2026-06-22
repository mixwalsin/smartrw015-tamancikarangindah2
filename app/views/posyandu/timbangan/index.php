<!-- Data Penimbangan -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('posyandu') ?>" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
            <span class="fs-5 fw-bold"><i class="bi bi-speedometer2 me-2 text-warning"></i>Data Penimbangan</span>
        </div>
        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
            <a href="<?= url('posyandu/timbangan/create') ?>" class="btn btn-warning">
                <i class="bi bi-plus-lg me-1"></i>Catat Timbangan
            </a>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Balita</th>
                            <th>Tgl Timbang</th>
                            <th>BB (kg)</th>
                            <th>TB (cm)</th>
                            <th>Status Gizi</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagination['data'])): ?>
                            <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            <?php foreach ($pagination['data'] as $row): ?>
                                <?php
                                $giziMap = [
                                    'gizi_baik'   => ['bg-success', 'Gizi Baik'],
                                    'gizi_kurang' => ['bg-warning text-dark', 'Gizi Kurang'],
                                    'gizi_buruk'  => ['bg-danger', 'Gizi Buruk'],
                                    'lebih'       => ['bg-info text-dark', 'Lebih'],
                                ];
                                [$giziCls, $giziLbl] = $giziMap[$row['status_gizi']] ?? ['bg-secondary', $row['status_gizi']];
                                $isAlert = in_array($row['status_gizi'], ['gizi_kurang', 'gizi_buruk']);
                                ?>
                                <tr class="<?= $isAlert ? 'table-warning' : '' ?>">
                                    <td><?= $no++ ?></td>
                                    <td class="fw-semibold">
                                        <a href="<?= url('posyandu/balita/show/' . $row['balita_id']) ?>" class="text-decoration-none">
                                            <?= e($row['nama_balita']) ?>
                                        </a>
                                        <?php if ($isAlert): ?>
                                            <i class="bi bi-exclamation-circle-fill text-danger ms-1"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($row['tanggal_timbang']) ?></td>
                                    <td><?= number_format((float)$row['berat_badan'], 1) ?></td>
                                    <td><?= $row['tinggi_badan'] !== null ? number_format((float)$row['tinggi_badan'], 1) : '-' ?></td>
                                    <td><span class="badge <?= $giziCls ?>"><?= $giziLbl ?></span></td>
                                    <td><?= e($row['catatan'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= url('posyandu/grafik/' . $row['balita_id']) ?>" class="btn btn-sm btn-outline-info" title="Grafik">
                                            <i class="bi bi-graph-up"></i>
                                        </a>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'petugas_posyandu'])): ?>
                                            <form action="<?= url('posyandu/timbangan/delete/' . $row['id']) ?>" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus data timbangan ini?')">
                                                <?= csrfField() ?>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data timbangan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pagination)): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">Total: <?= number_format($pagination['total']) ?> record timbangan</small>
                <?= paginate($pagination, 'posyandu/timbangan') ?>
            </div>
        <?php endif; ?>
    </div>

</div>
