<!-- Data Penduduk -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Data Penduduk</h4>
        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'])): ?>
            <a href="<?= url('penduduk/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah
            </a>
        <?php endif; ?>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?= url('penduduk') ?>" class="d-flex gap-2">
                <input type="text" name="keyword" class="form-control" placeholder="Cari NIK, nama, atau alamat..."
                       value="<?= e($pagination['keyword'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                <?php if (!empty($pagination['keyword'])): ?>
                    <a href="<?= url('penduduk') ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
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
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>RT</th>
                            <th>Jenis Kelamin</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagination['data'])): ?>
                            <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            <?php foreach ($pagination['data'] as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= e($row['nik']) ?></code></td>
                                    <td><?= e($row['nama']) ?></td>
                                    <td>RT <?= e($row['rt']) ?></td>
                                    <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                    <td><?= e(truncate($row['alamat'], 40)) ?></td>
                                    <td>
                                        <a href="<?= url('penduduk/show/' . $row['id']) ?>" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'])): ?>
                                            <a href="<?= url('penduduk/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw'])): ?>
                                            <form action="<?= url('penduduk/delete/' . $row['id']) ?>" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus data penduduk ini?')">
                                                <?= csrfField() ?>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data penduduk.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pagination)): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Total: <?= number_format($pagination['total']) ?> penduduk
                </small>
                <?= paginate($pagination, 'penduduk') ?>
            </div>
        <?php endif; ?>
    </div>

</div>
