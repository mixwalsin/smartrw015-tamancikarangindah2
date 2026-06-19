<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-shield me-2 text-primary"></i>Riwayat Patroli</h4>
        <a href="<?= url('security') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">Input Patroli</div>
        <div class="card-body">
            <form method="POST" action="<?= url('security/patroli/store') ?>" class="row g-2">
                <?= csrfField() ?>
                <div class="col-md-4"><input type="text" name="petugas_patroli" class="form-control" placeholder="Nama Petugas" required></div>
                <div class="col-md-4"><input type="text" name="lokasi_patroli" class="form-control" placeholder="Lokasi Patroli" required></div>
                <div class="col-md-2">
                    <select name="status_kondisi" class="form-select">
                        <option value="aman" selected>Aman</option>
                        <option value="rawan">Rawan</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid"><button class="btn btn-primary">Simpan</button></div>
                <div class="col-md-12"><input type="text" name="catatan" class="form-control" placeholder="Catatan"></div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Data Patroli</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Petugas</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Catatan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data ?? [] as $row): ?>
                            <tr>
                                <td><?= e((string) ($row['tanggal_patroli'] ?? '-')) ?> <?= e((string) ($row['jam_patroli'] ?? '')) ?></td>
                                <td><?= e((string) ($row['petugas_patroli'] ?? '-')) ?></td>
                                <td><?= e((string) ($row['lokasi_patroli'] ?? '-')) ?></td>
                                <td><?= e((string) ($row['status_kondisi'] ?? '-')) ?></td>
                                <td><?= e((string) ($row['catatan'] ?? '-')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($data)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data patroli.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Ringkasan Lokasi</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($summaryLokasi ?? [] as $row): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e((string) ($row['lokasi_patroli'] ?? '-')) ?></span>
                            <strong><?= number_format((int) ($row['total'] ?? 0)) ?></strong>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($summaryLokasi)): ?>
                        <li class="list-group-item text-muted">Belum ada ringkasan.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
