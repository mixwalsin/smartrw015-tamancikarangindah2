<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-card-checklist me-2 text-primary"></i>Data Kartu Keluarga</h4>
        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'], true)): ?>
            <a href="<?= url('kartu-keluarga/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah KK
            </a>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nomor KK</th>
                    <th>Kepala Keluarga</th>
                    <th>Alamat</th>
                    <th>RT/RW</th>
                    <th>Jumlah Anggota</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($pagination['data'])): ?>
                    <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                    <?php foreach ($pagination['data'] as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><code><?= e($row['nomor_kk']) ?></code></td>
                            <td><?= e($row['kepala_keluarga']) ?></td>
                            <td><?= e(truncate($row['alamat'], 45)) ?></td>
                            <td>RT <?= e($row['rt']) ?> / RW <?= e($row['rw']) ?></td>
                            <td><?= (int) ($row['jumlah_anggota_terhitung'] ?? $row['jumlah_anggota']) ?></td>
                            <td class="text-nowrap">
                                <a href="<?= url('kartu-keluarga/show/' . $row['id']) ?>" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= url('kartu-keluarga/pindah/' . $row['id']) ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-geo-alt"></i>
                                </a>
                                <a href="<?= url('kartu-keluarga/print/' . $row['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data kartu keluarga.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">Total: <?= (int) $pagination['total'] ?> KK</small>
            <?= paginate($pagination, 'kartu-keluarga') ?>
        </div>
    </div>
</div>
