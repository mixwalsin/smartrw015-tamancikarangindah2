<!-- Verifikasi Keaslian Surat via QR Code -->
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8 col-lg-6">

            <div class="text-center mb-4">
                <i class="bi bi-qr-code-scan display-3 text-primary"></i>
                <h4 class="fw-bold mt-2">Verifikasi Keaslian Surat</h4>
                <p class="text-muted">RW 015 Taman Cikarang Indah 2</p>
            </div>

            <?php if ($pengajuan): ?>
                <!-- Surat Valid -->
                <?php
                $isValid = in_array($pengajuan['status'], ['disetujui', 'selesai']);
                $badge   = $isValid ? 'success' : 'warning';
                $icon    = $isValid ? 'bi-patch-check-fill' : 'bi-exclamation-triangle-fill';
                ?>
                <div class="card border-0 shadow-sm border-<?= $badge ?> border-3">
                    <div class="card-header bg-<?= $badge ?> text-white text-center py-3">
                        <i class="bi <?= $icon ?> fs-2 d-block"></i>
                        <h5 class="fw-bold mb-0 mt-1">
                            <?= $isValid ? 'SURAT VALID & RESMI' : 'SURAT BELUM DISETUJUI' ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-<?= $badge ?> py-2">
                                    <strong>Status:</strong>
                                    <?= \PengajuanSuratModel::statusLabel($pengajuan['status']) ?>
                                    <?php if ($pengajuan['no_surat']): ?>
                                        &nbsp;|&nbsp; <strong>No. Surat:</strong> <?= e($pengajuan['no_surat']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="small text-muted">Jenis Surat</div>
                                <div class="fw-bold">
                                    <span class="badge bg-secondary"><?= e($pengajuan['jenis_kode']) ?></span>
                                    <?= e($pengajuan['jenis_nama']) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Tanggal Diajukan</div>
                                <div><?= e(date('d F Y', strtotime($pengajuan['created_at']))) ?></div>
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            <div class="col-md-6">
                                <div class="small text-muted">Nama Pemohon</div>
                                <div class="fw-semibold"><?= e($pengajuan['pemohon_nama']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">NIK</div>
                                <div><code><?= e($pengajuan['pemohon_nik']) ?></code></div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Alamat</div>
                                <div><?= e($pengajuan['pemohon_alamat']) ?>, RT <?= e($pengajuan['pemohon_rt']) ?>/RW015</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Keperluan</div>
                                <div><?= e(truncate($pengajuan['keperluan'], 80)) ?></div>
                            </div>

                            <?php if ($pengajuan['rw_approval_at']): ?>
                            <div class="col-12"><hr class="my-1"></div>
                            <div class="col-md-6">
                                <div class="small text-muted">Disetujui Oleh</div>
                                <div><?= e($pengajuan['rw_approval_nama'] ?? 'Pengurus RW') ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Tanggal Approval</div>
                                <div><?= e(date('d F Y H:i', strtotime($pengajuan['rw_approval_at']))) ?></div>
                            </div>
                            <?php endif; ?>

                            <div class="col-12 text-center mt-2">
                                <div class="small text-muted">Kode Verifikasi</div>
                                <code class="fs-6"><?= e($pengajuan['kode_verifikasi']) ?></code>
                            </div>
                        </div>
                    </div>
                    <?php if ($isValid): ?>
                    <div class="card-footer bg-light text-center small text-success">
                        <i class="bi bi-shield-check me-1"></i>
                        Surat ini telah diverifikasi dan disetujui oleh pengurus RW 015 Taman Cikarang Indah 2
                    </div>
                    <?php else: ?>
                    <div class="card-footer bg-light text-center small text-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Surat ini belum mendapatkan persetujuan resmi
                    </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <!-- Surat Tidak Ditemukan -->
                <div class="card border-0 shadow-sm border-danger border-3">
                    <div class="card-header bg-danger text-white text-center py-3">
                        <i class="bi bi-x-circle-fill fs-2 d-block"></i>
                        <h5 class="fw-bold mb-0 mt-1">SURAT TIDAK VALID</h5>
                    </div>
                    <div class="card-body text-center p-4">
                        <p class="text-muted mb-3">
                            Surat dengan kode verifikasi <code><?= e($kode) ?></code>
                            tidak ditemukan dalam sistem kami.
                        </p>
                        <p class="small text-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Kemungkinan surat ini palsu atau kode verifikasi tidak valid.
                        </p>
                    </div>
                    <div class="card-footer bg-light text-center small text-danger">
                        <i class="bi bi-shield-x me-1"></i>
                        Harap hubungi pengurus RW 015 untuk konfirmasi keaslian surat
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="<?= url('/') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-house me-1"></i>Beranda Smart RW015
                </a>
            </div>

        </div>
    </div>
</div>
