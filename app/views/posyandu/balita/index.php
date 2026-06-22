<!-- Data Balita -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('posyandu') ?>" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <span class="fs-5 fw-bold"><i class="bi bi-emoji-smile me-2 text-primary"></i>Data Balita</span>
        </div>
        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
            <a href="<?= url('posyandu/balita/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Balita
            </a>
        <?php endif; ?>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?= url('posyandu/balita') ?>" class="d-flex gap-2">
                <input type="text" name="keyword" class="form-control" placeholder="Cari nama balita, nama ibu, atau RT..."
                       value="<?= e($pagination['keyword'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                <?php if (!empty($pagination['keyword'])): ?>
                    <a href="<?= url('posyandu/balita') ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>JK</th>
                            <th>Tgl Lahir</th>
                            <th>RT</th>
                            <th>Nama Ibu</th>
                            <th>BB (kg)</th>
                            <th>Imunisasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagination['data'])): ?>
                            <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            <?php foreach ($pagination['data'] as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-semibold"><?= e($row['nama']) ?></td>
                                    <td><?= $row['jenis_kelamin'] === 'L' ? '<span class="badge bg-primary">L</span>' : '<span class="badge bg-danger">P</span>' ?></td>
                                    <td><?= $row['tgl_lahir'] ? formatDate($row['tgl_lahir']) : '-' ?></td>
                                    <td>RT <?= e($row['rt']) ?></td>
                                    <td><?= e($row['nama_ibu']) ?></td>
                                    <td><?= $row['berat_badan'] !== null ? number_format((float)$row['berat_badan'], 1) : '-' ?></td>
                                    <td>
                                        <?php
                                        $statusMap = [
                                            'lengkap'        => ['class' => 'bg-success', 'label' => 'Lengkap'],
                                            'tidak_lengkap'  => ['class' => 'bg-warning text-dark', 'label' => 'Belum Lengkap'],
                                            'belum'          => ['class' => 'bg-secondary', 'label' => 'Belum'],
                                        ];
                                        $st = $statusMap[$row['status_imunisasi']] ?? $statusMap['belum'];
                                        ?>
                                        <span class="badge <?= $st['class'] ?>"><?= $st['label'] ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= url('posyandu/balita/show/' . $row['id']) ?>" class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
                                            <a href="<?= url('posyandu/balita/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= url('posyandu/imunisasi/create?balita_id=' . $row['id']) ?>" class="btn btn-sm btn-outline-success" title="Tambah Imunisasi">
                                            <i class="bi bi-shield-plus"></i>
                                        </a>
                                        <a href="<?= url('posyandu/timbangan/create?balita_id=' . $row['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Timbang">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'petugas_posyandu'])): ?>
                                            <form action="<?= url('posyandu/balita/delete/' . $row['id']) ?>" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus data balita ini?')">
                                                <?= csrfField() ?>
                                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data balita.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pagination)): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">Total: <?= number_format($pagination['total']) ?> balita</small>
                <?= paginate($pagination, 'posyandu/balita') ?>
            </div>
        <?php endif; ?>
    </div>

</div>
