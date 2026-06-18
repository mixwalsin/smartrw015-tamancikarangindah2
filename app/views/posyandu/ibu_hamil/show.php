<!-- Detail Ibu Hamil -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('posyandu/ibu-hamil') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Detail Ibu Hamil</h4>
                <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
                    <a href="<?= url('posyandu/ibu-hamil/edit/' . $ibuHamil['id']) ?>" class="btn btn-sm btn-outline-warning ms-auto">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($ibuHamil['status_kesehatan'] === 'berisiko_tinggi'): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    <strong>Status Berisiko Tinggi</strong> &mdash; Perlu perhatian khusus.
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold bg-<?= $ibuHamil['status_kesehatan'] === 'berisiko_tinggi' ? 'danger' : 'primary' ?> text-white">
                    <i class="bi bi-person-heart me-2"></i><?= e($ibuHamil['nama']) ?>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Nama</td>
                            <td class="fw-semibold"><?= e($ibuHamil['nama']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Umur</td>
                            <td><?= $ibuHamil['umur'] ?> tahun</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bulan Kehamilan</td>
                            <td>Bulan ke-<?= $ibuHamil['bulan_kehamilan'] ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Perkiraan Lahir</td>
                            <td><?= $ibuHamil['tgl_perkiraan_lahir'] ? formatDate($ibuHamil['tgl_perkiraan_lahir']) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td><?= e($ibuHamil['alamat'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Rumah</td>
                            <td><?= e($ibuHamil['no_rumah'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">RT/RW</td>
                            <td>RT <?= e($ibuHamil['rt']) ?> / RW <?= e($ibuHamil['rw']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Kesehatan</td>
                            <td>
                                <?php if ($ibuHamil['status_kesehatan'] === 'berisiko_tinggi'): ?>
                                    <span class="badge bg-danger">Berisiko Tinggi</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Normal</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if ($ibuHamil['catatan']): ?>
                        <tr>
                            <td class="text-muted">Catatan</td>
                            <td><?= e($ibuHamil['catatan']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">Dicatat</td>
                            <td><?= formatDate($ibuHamil['created_at'], 'd F Y H:i') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
