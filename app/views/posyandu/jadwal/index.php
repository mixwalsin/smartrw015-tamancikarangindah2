<!-- Jadwal Posyandu -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('posyandu') ?>" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i></a>
            <span class="fs-5 fw-bold"><i class="bi bi-calendar3 me-2 text-success"></i>Jadwal Posyandu</span>
        </div>
        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
            <a href="<?= url('posyandu/jadwal/create') ?>" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i>Buat Jadwal
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
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Lokasi</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagination['data'])): ?>
                            <?php $no = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            <?php foreach ($pagination['data'] as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= formatDate($row['tanggal']) ?></td>
                                    <td><?= substr($row['jam_mulai'], 0, 5) ?> &ndash; <?= substr($row['jam_selesai'], 0, 5) ?></td>
                                    <td><?= e($row['lokasi']) ?></td>
                                    <td><?= e($row['keterangan'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $statusMap = [
                                            'dijadwalkan' => ['bg-primary', 'Dijadwalkan'],
                                            'berlangsung' => ['bg-success', 'Berlangsung'],
                                            'selesai'     => ['bg-secondary', 'Selesai'],
                                            'dibatalkan'  => ['bg-danger', 'Dibatalkan'],
                                        ];
                                        [$cls, $lbl] = $statusMap[$row['status']] ?? ['bg-secondary', $row['status']];
                                        ?>
                                        <span class="badge <?= $cls ?>"><?= $lbl ?></span>
                                    </td>
                                    <td>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
                                            <a href="<?= url('posyandu/jadwal/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                        <?php endif; ?>
                                        <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'petugas_posyandu'])): ?>
                                            <form action="<?= url('posyandu/jadwal/delete/' . $row['id']) ?>" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus jadwal ini?')">
                                                <?= csrfField() ?>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada jadwal posyandu.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pagination)): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">Total: <?= number_format($pagination['total']) ?> jadwal</small>
                <?= paginate($pagination, 'posyandu/jadwal') ?>
            </div>
        <?php endif; ?>
    </div>

</div>
