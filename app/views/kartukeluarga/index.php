<!-- Daftar Kartu Keluarga -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-house-door me-2 text-primary"></i>Kartu Keluarga</h4>
        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'])): ?>
            <a href="<?= url('kartukeluarga/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah KK
            </a>
        <?php endif; ?>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?= url('kartukeluarga') ?>" class="d-flex gap-2">
                <input type="text" name="keyword" class="form-control"
                       placeholder="Cari No. KK, kepala keluarga, atau alamat..."
                       value="<?= e($pagination['keyword'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                <?php if (!empty($pagination['keyword'])): ?>
                    <a href="<?= url('kartukeluarga') ?>" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
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
                            <th>No. KK</th>
                            <th>Kepala Keluarga</th>
                            <th>RT</th>
                            <th>Alamat</th>
                            <th class="text-center">Anggota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagination['data'])): ?>
                            <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            <?php foreach ($pagination['data'] as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= e($row['no_kk']) ?></code></td>
                                    <td><?= e($row['kepala_keluarga'] ?? '—') ?></td>
                                    <td>RT <?= e($row['rt_text'] ?? '—') ?></td>
                                    <td><?= e(truncate($row['alamat'], 40)) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?= (int)($row['jumlah_anggota'] ?? 0) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= url('kartukeluarga/show/' . $row['id']) ?>"
                                           class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'])): ?>
                                            <a href="<?= url('kartukeluarga/edit/' . $row['id']) ?>"
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= url('kartukeluarga/cetak/' . $row['id']) ?>"
                                           class="btn btn-sm btn-outline-secondary" title="Cetak" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada data Kartu Keluarga.
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
                    Total: <?= number_format($pagination['total']) ?> KK
                </small>
                <?= paginate($pagination, 'kartukeluarga') ?>
            </div>
        <?php endif; ?>
    </div>

</div>
