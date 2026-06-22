<!-- Riwayat Perubahan KK -->
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="<?= url('kartukeluarga/show/' . $kk['id']) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-clock-history me-2 text-info"></i>Riwayat Perubahan KK
            </h4>
            <small class="text-muted">KK <?= e($kk['no_kk']) ?> &mdash; <?= e($kk['kepala_keluarga'] ?? '—') ?></small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($riwayat)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                                <th>Warga Terkait</th>
                                <th>Keterangan</th>
                                <th>Dilakukan Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat as $i => $r): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="small text-muted">
                                        <?= e(formatDate($r['created_at'], 'd M Y H:i')) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $aksiLabel = [
                                            'tambah_kk'      => ['label' => 'Tambah KK',      'color' => 'success'],
                                            'ubah_kk'        => ['label' => 'Ubah Data KK',   'color' => 'warning'],
                                            'tambah_anggota' => ['label' => 'Tambah Anggota', 'color' => 'primary'],
                                            'pindah_kk'      => ['label' => 'Pindah KK',      'color' => 'info'],
                                            'hapus_anggota'  => ['label' => 'Hapus Anggota',  'color' => 'danger'],
                                        ];
                                        $a = $aksiLabel[$r['aksi']] ?? ['label' => ucwords(str_replace('_', ' ', $r['aksi'])), 'color' => 'secondary'];
                                        ?>
                                        <span class="badge bg-<?= $a['color'] ?>">
                                            <?= $a['label'] ?>
                                        </span>
                                    </td>
                                    <td><?= $r['nama_warga'] ? e($r['nama_warga']) : '<span class="text-muted">—</span>' ?></td>
                                    <td><?= e($r['keterangan'] ?? '—') ?></td>
                                    <td class="small"><?= $r['nama_user'] ? e($r['nama_user']) : '<span class="text-muted">Sistem</span>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-clock-history display-4 text-muted opacity-25 d-block mb-3"></i>
                    Belum ada riwayat perubahan untuk KK ini.
                </div>
            <?php endif; ?>
        </div>
        <div class="card-footer bg-white text-muted small">
            Total <?= count($riwayat) ?> catatan riwayat.
        </div>
    </div>

</div>
