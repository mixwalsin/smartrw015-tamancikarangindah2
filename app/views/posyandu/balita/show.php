<!-- Detail Balita -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('posyandu/balita') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Detail Balita</h4>
                <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
                    <a href="<?= url('posyandu/balita/edit/' . $balita['id']) ?>" class="btn btn-sm btn-outline-warning ms-auto">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                <?php endif; ?>
            </div>

            <div class="row g-3">
                <!-- Info Dasar -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="bi bi-person-fill me-2"></i>Data Pribadi
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" style="width:40%">Nama</td>
                                    <td class="fw-semibold"><?= e($balita['nama']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jenis Kelamin</td>
                                    <td><?= $balita['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tgl Lahir</td>
                                    <td><?= formatDate($balita['tgl_lahir']) ?></td>
                                </tr>
                                <?php
                                $tgl = new \DateTime($balita['tgl_lahir']);
                                $now = new \DateTime();
                                $diff = $tgl->diff($now);
                                $umurBulan = $diff->y * 12 + $diff->m;
                                ?>
                                <tr>
                                    <td class="text-muted">Umur</td>
                                    <td><?= $diff->y > 0 ? $diff->y . ' th ' : '' ?><?= $diff->m ?> bln</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama Ibu</td>
                                    <td><?= e($balita['nama_ibu']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama Ayah</td>
                                    <td><?= e($balita['nama_ayah'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat</td>
                                    <td><?= e($balita['alamat'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. Rumah</td>
                                    <td><?= e($balita['no_rumah'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">RT/RW</td>
                                    <td>RT <?= e($balita['rt']) ?> / RW <?= e($balita['rw']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Status Kesehatan -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-success text-white fw-semibold">
                            <i class="bi bi-activity me-2"></i>Status Kesehatan
                        </div>
                        <div class="card-body">
                            <div class="row text-center g-3">
                                <div class="col-4">
                                    <div class="fs-3 fw-bold text-primary">
                                        <?= $balita['berat_badan'] !== null ? number_format((float)$balita['berat_badan'], 1) : '-' ?>
                                    </div>
                                    <div class="small text-muted">BB (kg)</div>
                                </div>
                                <div class="col-4">
                                    <div class="fs-3 fw-bold text-success">
                                        <?= $balita['tinggi_badan'] !== null ? number_format((float)$balita['tinggi_badan'], 1) : '-' ?>
                                    </div>
                                    <div class="small text-muted">TB (cm)</div>
                                </div>
                                <div class="col-4">
                                    <?php
                                    $statusMap = [
                                        'lengkap'       => ['bg-success', 'Lengkap'],
                                        'tidak_lengkap' => ['bg-warning text-dark', 'Belum Lengkap'],
                                        'belum'         => ['bg-secondary', 'Belum'],
                                    ];
                                    [$badgeCls, $badgeLbl] = $statusMap[$balita['status_imunisasi']] ?? ['bg-secondary', 'Belum'];
                                    ?>
                                    <span class="badge <?= $badgeCls ?> fs-6"><?= $badgeLbl ?></span>
                                    <div class="small text-muted mt-1">Imunisasi</div>
                                </div>
                            </div>
                            <?php if ($balita['catatan']): ?>
                                <hr>
                                <p class="mb-0 small text-muted"><i class="bi bi-journal-text me-1"></i><?= e($balita['catatan']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt', 'petugas_posyandu'])): ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= url('posyandu/imunisasi/create?balita_id=' . $balita['id']) ?>" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-shield-plus me-1"></i>Tambah Imunisasi
                        </a>
                        <a href="<?= url('posyandu/timbangan/create?balita_id=' . $balita['id']) ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-speedometer2 me-1"></i>Catat Timbangan
                        </a>
                        <a href="<?= url('posyandu/grafik/' . $balita['id']) ?>" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-graph-up me-1"></i>Grafik Pertumbuhan
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Imunisasi -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom fw-semibold">
                            <i class="bi bi-shield-plus text-success me-2"></i>Riwayat Imunisasi
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($imunisasi)): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($imunisasi as $im): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-semibold"><?= e($im['jenis_imunisasi']) ?></div>
                                                <small class="text-muted"><?= formatDate($im['tanggal_imunisasi']) ?> &bull; <?= e($im['tempat_imunisasi'] ?? '-') ?></small>
                                            </div>
                                            <?php if (in_array(authUser()['role'] ?? '', ['admin', 'rw', 'petugas_posyandu'])): ?>
                                                <form action="<?= url('posyandu/imunisasi/delete/' . $im['id']) ?>" method="POST"
                                                      onsubmit="return confirm('Hapus?')">
                                                    <?= csrfField() ?>
                                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center text-muted py-3">Belum ada imunisasi.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Timbangan -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom fw-semibold">
                            <i class="bi bi-speedometer2 text-warning me-2"></i>Riwayat Timbangan
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($timbangan)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tgl</th>
                                                <th>BB</th>
                                                <th>TB</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_reverse($timbangan) as $t): ?>
                                                <?php
                                                $giziMap = [
                                                    'gizi_baik'   => 'bg-success',
                                                    'gizi_kurang' => 'bg-warning text-dark',
                                                    'gizi_buruk'  => 'bg-danger',
                                                    'lebih'       => 'bg-info text-dark',
                                                ];
                                                $giziCls = $giziMap[$t['status_gizi']] ?? 'bg-secondary';
                                                ?>
                                                <tr>
                                                    <td><?= date('d/m/y', strtotime($t['tanggal_timbang'])) ?></td>
                                                    <td><?= number_format((float)$t['berat_badan'], 1) ?> kg</td>
                                                    <td><?= $t['tinggi_badan'] !== null ? number_format((float)$t['tinggi_badan'], 1) . ' cm' : '-' ?></td>
                                                    <td><span class="badge <?= $giziCls ?>"><?= str_replace('_', ' ', $t['status_gizi']) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-3">Belum ada data timbangan.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
