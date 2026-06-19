<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-qr-code-scan me-2 text-primary"></i>QR Gate Pass</h4>
        <a href="<?= url('security') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">Data Gate Pass</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Tanggal Berlaku</th>
                    <th>Jam Berlaku</th>
                    <th>Payload</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data ?? [] as $row): ?>
                    <tr>
                        <td><?= (int) ($row['id'] ?? 0) ?></td>
                        <td><?= e((string) ($row['status'] ?? '-')) ?></td>
                        <td><?= e((string) ($row['tanggal_berlaku'] ?? '-')) ?></td>
                        <td><?= e((string) ($row['jam_berlaku_mulai'] ?? '-')) ?> - <?= e((string) ($row['jam_berlaku_selesai'] ?? '-')) ?></td>
                        <td><small><code><?= e((string) truncate((string) ($row['qr_code'] ?? ''), 60)) ?></code></small></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada gate pass.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
