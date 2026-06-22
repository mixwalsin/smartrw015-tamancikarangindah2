<!-- Data Ibu Hamil -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('posyandu') ?>" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
            <span class="fs-5 fw-bold"><i class="bi bi-person-heart me-2 text-danger"></i>Data Ibu Hamil</span>
        </div>
        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
            <a href="<?= url('posyandu/ibu-hamil/create') ?>" class="btn btn-danger">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </a>
        <?php endif; ?>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?= url('posyandu/ibu-hamil') ?>" class="d-flex gap-2">
                <input type="text" name="keyword" class="form-control" placeholder="Cari nama atau RT..."
                       value="<?= e($pagination['keyword'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-search"></i></button>
                <?php if (!empty($pagination['keyword'])): ?>
                    <a href="<?= url('posyandu/ibu-hamil') ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
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
                            <th>Umur</th>
                            <th>RT</th>
                            <th>Bln Kehamilan</th>
                            <th>Perkiraan Lahir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagination['data'])): ?>
                            <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            <?php foreach ($pagination['data'] as $row): ?>
                                <tr class="<?= $row['status_kesehatan'] === 'berisiko_tinggi' ? 'table-danger' : '' ?>">
                                    <td><?= $no++ ?></td>
                                    <td class="fw-semibold">
                                        <?= e($row['nama']) ?>
                                        <?php if ($row['status_kesehatan'] === 'berisiko_tinggi'): ?>
                                            <i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="Berisiko Tinggi"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $row['umur'] ?> thn</td>
                                    <td>RT <?= e($row['rt']) ?></td>
                                    <td><?= $row['bulan_kehamilan'] ?> bulan</td>
                                    <td><?= $row['tgl_perkiraan_lahir'] ? formatDate($row['tgl_perkiraan_lahir']) : '-' ?></td>
                                    <td>
                                        <?php if ($row['status_kesehatan'] === 'berisiko_tinggi'): ?>
                                            <span class="badge bg-danger">Berisiko Tinggi</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= url('posyandu/ibu-hamil/show/' . $row['id']) ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
                                            <a href="<?= url('posyandu/ibu-hamil/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                        <?php endif; ?>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'petugas_posyandu'])): ?>
                                            <form action="<?= url('posyandu/ibu-hamil/delete/' . $row['id']) ?>" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus data ibu hamil ini?')">
                                                <?= csrfField() ?>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data ibu hamil.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pagination)): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">Total: <?= number_format($pagination['total']) ?> ibu hamil</small>
                <?= paginate($pagination, 'posyandu/ibu-hamil') ?>
            </div>
        <?php endif; ?>
    </div>

</div>
