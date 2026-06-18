<!-- Data Imunisasi -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('posyandu') ?>" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
            <span class="fs-5 fw-bold"><i class="bi bi-shield-plus me-2 text-info"></i>Data Imunisasi</span>
        </div>
        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
            <a href="<?= url('posyandu/imunisasi/create') ?>" class="btn btn-info text-white">
                <i class="bi bi-plus-lg me-1"></i>Tambah Imunisasi
            </a>
        <?php endif; ?>
    </div>

    <!-- Checklist Imunisasi Wajib -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-list-check text-info me-2"></i>Imunisasi Dasar Wajib (Kemenkes)
        </div>
        <div class="card-body">
            <div class="row g-2">
                <?php
                $imunisasiWajib = ['HB-0','BCG','DPT-HB-Hib 1','DPT-HB-Hib 2','DPT-HB-Hib 3','Polio 1','Polio 2','Polio 3','Polio 4','IPV','Campak/MR'];
                foreach ($imunisasiWajib as $im):
                ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2 w-100 d-block text-center">
                            <i class="bi bi-check-circle me-1"></i><?= $im ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Balita</th>
                            <th>Jenis Imunisasi</th>
                            <th>Tgl Imunisasi</th>
                            <th>Tempat</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagination['data'])): ?>
                            <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            <?php foreach ($pagination['data'] as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-semibold"><?= e($row['nama_balita']) ?></td>
                                    <td><span class="badge bg-info text-dark"><?= e($row['jenis_imunisasi']) ?></span></td>
                                    <td><?= formatDate($row['tanggal_imunisasi']) ?></td>
                                    <td><?= e($row['tempat_imunisasi'] ?? '-') ?></td>
                                    <td><?= e($row['catatan'] ?? '-') ?></td>
                                    <td>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'petugas_posyandu'])): ?>
                                            <form action="<?= url('posyandu/imunisasi/delete/' . $row['id']) ?>" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus data imunisasi ini?')">
                                                <?= csrfField() ?>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data imunisasi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pagination)): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">Total: <?= number_format($pagination['total']) ?> record imunisasi</small>
                <?= paginate($pagination, 'posyandu/imunisasi') ?>
            </div>
        <?php endif; ?>
    </div>

</div>
