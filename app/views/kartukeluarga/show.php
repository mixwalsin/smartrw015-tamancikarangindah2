<!-- Detail Kartu Keluarga -->
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex align-items-center mb-4 gap-3 flex-wrap">
        <a href="<?= url('kartukeluarga') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0 me-auto">
            <i class="bi bi-house-door me-2 text-primary"></i>KK <?= e($kk['no_kk']) ?>
        </h4>
        <div class="d-flex gap-2">
            <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'])): ?>
                <a href="<?= url('kartukeluarga/tambah-anggota/' . $kk['id']) ?>"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Tambah Anggota
                </a>
                <a href="<?= url('kartukeluarga/pindah/' . $kk['id']) ?>"
                   class="btn btn-warning btn-sm">
                    <i class="bi bi-arrow-left-right me-1"></i>Pindah KK
                </a>
                <a href="<?= url('kartukeluarga/edit/' . $kk['id']) ?>"
                   class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            <?php endif; ?>
            <a href="<?= url('kartukeluarga/cetak/' . $kk['id']) ?>"
               class="btn btn-outline-secondary btn-sm" target="_blank">
                <i class="bi bi-printer me-1"></i>Cetak
            </a>
            <a href="<?= url('kartukeluarga/riwayat/' . $kk['id']) ?>"
               class="btn btn-outline-info btn-sm">
                <i class="bi bi-clock-history me-1"></i>Riwayat
            </a>
        </div>
    </div>

    <div class="row g-4">

        <!-- Info KK -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-info-circle me-1"></i>Informasi Kartu Keluarga
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">No. KK</td>
                            <td><strong><code><?= e($kk['no_kk']) ?></code></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kepala Keluarga</td>
                            <td><?= e($kk['kepala_keluarga'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td><?= e($kk['alamat']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">RT</td>
                            <td>RT <?= e($kk['rt_text'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">RW</td>
                            <td>RW 015</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah Anggota</td>
                            <td>
                                <span class="badge bg-primary"><?= (int)($kk['jumlah_anggota'] ?? 0) ?> orang</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Anggota Keluarga -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-people me-1 text-primary"></i>Anggota Keluarga
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Hubungan</th>
                                    <th>JK</th>
                                    <th>Tgl. Lahir</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($anggota)): ?>
                                    <?php foreach ($anggota as $i => $a): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><code class="small"><?= e($a['nik']) ?></code></td>
                                            <td>
                                                <?= e($a['nama']) ?>
                                                <?php if ($a['hubungan'] === 'Kepala Keluarga'): ?>
                                                    <span class="badge bg-primary ms-1">KK</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?= e($a['hubungan']) ?>
                                                </span>
                                            </td>
                                            <td><?= $a['jenis_kelamin'] === 'L' ? 'L' : 'P' ?></td>
                                            <td class="small">
                                                <?= $a['tanggal_lahir'] ? e(formatDate($a['tanggal_lahir'])) : '—' ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusColor = match ($a['status_warga'] ?? 'tetap') {
                                                    'tetap'    => 'success',
                                                    'pendatang'=> 'info',
                                                    'pindah'   => 'warning',
                                                    'meninggal'=> 'danger',
                                                    default    => 'secondary',
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusColor ?>">
                                                    <?= ucfirst($a['status_warga'] ?? 'tetap') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            Belum ada anggota.
                                            <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'])): ?>
                                                <a href="<?= url('kartukeluarga/tambah-anggota/' . $kk['id']) ?>">
                                                    Tambah anggota
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
