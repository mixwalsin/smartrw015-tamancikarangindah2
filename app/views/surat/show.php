<!-- Detail Pengajuan Surat -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="<?= url('surat') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Detail Pengajuan Surat</h4>
                <?php if (in_array($pengajuan['status'], ['disetujui', 'selesai'])): ?>
                    <a href="<?= url('surat/print/' . $pengajuan['id']) ?>" class="btn btn-success btn-sm ms-auto" target="_blank">
                        <i class="bi bi-printer me-1"></i>Cetak Surat
                    </a>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <!-- Kolom Kiri: Info Pengajuan -->
                <div class="col-lg-8">

                    <!-- Status Header -->
                    <?php
                    $badge = \PengajuanSuratModel::statusBadge($pengajuan['status']);
                    $label = \PengajuanSuratModel::statusLabel($pengajuan['status']);
                    ?>
                    <div class="alert alert-<?= $badge ?> d-flex align-items-center gap-3 mb-4">
                        <i class="bi bi-info-circle fs-4"></i>
                        <div>
                            <strong>Status:</strong> <?= $label ?>
                            <?php if ($pengajuan['no_surat']): ?>
                                &nbsp;|&nbsp; <strong>No. Surat:</strong> <?= e($pengajuan['no_surat']) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Data Pemohon -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light fw-bold">
                            <i class="bi bi-person me-1"></i>Data Pemohon
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="small text-muted">Nama Lengkap</div>
                                    <div class="fw-semibold"><?= e($pengajuan['pemohon_nama']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">NIK</div>
                                    <div class="fw-semibold"><code><?= e($pengajuan['pemohon_nik']) ?></code></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Tempat / Tgl Lahir</div>
                                    <div><?= e($pengajuan['pemohon_tempat_lahir'] ?? '-') ?>,
                                        <?= $pengajuan['pemohon_tgl_lahir'] ? formatDate($pengajuan['pemohon_tgl_lahir']) : '-' ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted">Jenis Kelamin</div>
                                    <div><?= $pengajuan['pemohon_jk'] === 'L' ? 'Laki-laki' : ($pengajuan['pemohon_jk'] === 'P' ? 'Perempuan' : '-') ?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted">RT</div>
                                    <div>RT <?= e($pengajuan['pemohon_rt']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Agama</div>
                                    <div><?= e($pengajuan['pemohon_agama'] ?? '-') ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Pekerjaan</div>
                                    <div><?= e($pengajuan['pemohon_pekerjaan'] ?? '-') ?></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">No. HP</div>
                                    <div><?= e($pengajuan['pemohon_no_hp'] ?? '-') ?></div>
                                </div>
                                <div class="col-12">
                                    <div class="small text-muted">Alamat</div>
                                    <div><?= e($pengajuan['pemohon_alamat']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keperluan & Keterangan -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light fw-bold">
                            <i class="bi bi-card-text me-1"></i>Keperluan & Keterangan
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="small text-muted mb-1">Keperluan</div>
                                <p class="mb-0"><?= nl2br(e($pengajuan['keperluan'])) ?></p>
                            </div>
                            <?php if ($pengajuan['keterangan_tambahan']): ?>
                            <div>
                                <div class="small text-muted mb-1">Keterangan Tambahan</div>
                                <p class="mb-0"><?= nl2br(e($pengajuan['keterangan_tambahan'])) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if ($pengajuan['lampiran']): ?>
                            <div class="mt-2">
                                <div class="small text-muted mb-1">Lampiran</div>
                                <a href="<?= url('storage/uploads/' . $pengajuan['lampiran']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-paperclip me-1"></i>Lihat Lampiran
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Timeline Proses -->
                    <?php if (!empty($history)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light fw-bold">
                            <i class="bi bi-clock-history me-1"></i>Riwayat Proses
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php foreach ($history as $h): ?>
                                    <?php $badgeH = \PengajuanSuratModel::statusBadge($h['status_baru']); ?>
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="flex-shrink-0">
                                            <span class="badge rounded-circle bg-<?= $badgeH ?> p-2">
                                                <i class="bi bi-check-lg"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= \PengajuanSuratModel::statusLabel($h['status_baru']) ?></div>
                                            <?php if ($h['catatan']): ?>
                                                <div class="small text-muted"><?= e($h['catatan']) ?></div>
                                            <?php endif; ?>
                                            <div class="small text-muted">
                                                Oleh: <?= e($h['user_nama']) ?>
                                                &mdash; <?= e(date('d/m/Y H:i', strtotime($h['created_at']))) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Kolom Kanan: Aksi & Info -->
                <div class="col-lg-4">

                    <!-- Info Jenis Surat -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-primary text-white">
                            <i class="bi bi-envelope me-1"></i>Jenis Surat
                        </div>
                        <div class="card-body text-center py-3">
                            <span class="badge bg-secondary fs-6 mb-2"><?= e($pengajuan['jenis_kode']) ?></span>
                            <div class="fw-bold"><?= e($pengajuan['jenis_nama']) ?></div>
                            <div class="small text-muted mt-1">
                                Diajukan: <?= e(date('d/m/Y H:i', strtotime($pengajuan['created_at']))) ?>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code (jika sudah disetujui) -->
                    <?php if (in_array($pengajuan['status'], ['disetujui', 'selesai'])): ?>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            <i class="bi bi-qr-code me-1"></i>QR Verifikasi
                        </div>
                        <div class="card-body text-center">
                            <div id="qrcode" class="mb-2"></div>
                            <div class="small text-muted">Scan untuk verifikasi keaslian surat</div>
                            <div class="mt-1">
                                <code class="small"><?= e($pengajuan['kode_verifikasi']) ?></code>
                            </div>
                            <a href="<?= url('surat/verify/' . $pengajuan['kode_verifikasi']) ?>" class="btn btn-sm btn-outline-dark mt-2" target="_blank">
                                <i class="bi bi-link me-1"></i>Link Verifikasi
                            </a>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
                    <script>
                    new QRCode(document.getElementById('qrcode'), {
                        text: '<?= url('surat/verify/' . $pengajuan['kode_verifikasi']) ?>',
                        width: 150,
                        height: 150,
                        colorDark: '#000',
                        colorLight: '#fff',
                        correctLevel: QRCode.CorrectLevel.M
                    });
                    </script>
                    <?php endif; ?>

                    <!-- Aksi RT Verifikasi -->
                    <?php if ($pengajuan['status'] === 'menunggu_rt' && in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'])): ?>
                    <div class="card border-0 shadow-sm border-warning mb-3">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="bi bi-person-badge me-1"></i>Verifikasi RT
                        </div>
                        <div class="card-body">
                            <form action="<?= url('surat/verify-rt/' . $pengajuan['id']) ?>" method="POST">
                                <?= csrfField() ?>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Catatan</label>
                                    <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                              placeholder="Catatan verifikasi (opsional)"></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="action" value="verify" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle me-1"></i>Verifikasi & Teruskan ke RW
                                    </button>
                                    <button type="submit" name="action" value="reject"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                                        <i class="bi bi-x-circle me-1"></i>Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Aksi RW Approval -->
                    <?php if ($pengajuan['status'] === 'menunggu_rw' && in_array(authUser()['role'] ?? '', ['admin', 'rw'])): ?>
                    <div class="card border-0 shadow-sm border-info mb-3">
                        <div class="card-header bg-info text-white fw-bold">
                            <i class="bi bi-building me-1"></i>Approval RW
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success alert-sm py-2 small mb-3">
                                <i class="bi bi-check me-1"></i>Telah diverifikasi oleh RT
                                <?php if ($pengajuan['rt_verifikasi_nama']): ?>
                                    (<?= e($pengajuan['rt_verifikasi_nama']) ?>)
                                <?php endif; ?>
                            </div>
                            <form action="<?= url('surat/approve/' . $pengajuan['id']) ?>" method="POST">
                                <?= csrfField() ?>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Catatan Approval</label>
                                    <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                              placeholder="Catatan (opsional)"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-check-circle me-1"></i>Setujui & Terbitkan Surat
                                </button>
                            </form>
                            <form action="<?= url('surat/reject/' . $pengajuan['id']) ?>" method="POST" class="mt-2">
                                <?= csrfField() ?>
                                <div class="mb-2">
                                    <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                              placeholder="Alasan penolakan (wajib)"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100"
                                        onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                                    <i class="bi bi-x-circle me-1"></i>Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tandai Selesai -->
                    <?php if ($pengajuan['status'] === 'disetujui' && in_array(authUser()['role'] ?? '', ['admin', 'rw', 'rt'])): ?>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body text-center">
                            <form action="<?= url('surat/selesai/' . $pengajuan['id']) ?>" method="POST">
                                <?= csrfField() ?>
                                <button type="submit" class="btn btn-primary btn-sm"
                                        onclick="return confirm('Tandai surat ini sebagai selesai (sudah diterima)?')">
                                    <i class="bi bi-check2-all me-1"></i>Tandai Selesai
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Info RT/RW -->
                    <?php if ($pengajuan['rt_verifikasi_nama']): ?>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-light small fw-bold">Verifikasi RT</div>
                        <div class="card-body py-2">
                            <div class="small"><strong>Oleh:</strong> <?= e($pengajuan['rt_verifikasi_nama']) ?></div>
                            <div class="small"><strong>Waktu:</strong> <?= e(date('d/m/Y H:i', strtotime($pengajuan['rt_verifikasi_at']))) ?></div>
                            <?php if ($pengajuan['rt_catatan']): ?>
                                <div class="small text-muted mt-1"><?= e($pengajuan['rt_catatan']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($pengajuan['rw_approval_nama']): ?>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-light small fw-bold">Approval RW</div>
                        <div class="card-body py-2">
                            <div class="small"><strong>Oleh:</strong> <?= e($pengajuan['rw_approval_nama']) ?></div>
                            <div class="small"><strong>Waktu:</strong> <?= e(date('d/m/Y H:i', strtotime($pengajuan['rw_approval_at']))) ?></div>
                            <?php if ($pengajuan['rw_catatan']): ?>
                                <div class="small text-muted mt-1"><?= e($pengajuan['rw_catatan']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>
