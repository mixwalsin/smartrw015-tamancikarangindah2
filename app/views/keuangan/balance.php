<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Saldo Kas RW dan RT</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead class="table-light"><tr><th>Kas</th><th>RT</th><th>Saldo</th><th>Update Terakhir</th></tr></thead>
                <tbody>
                <?php foreach ($balances as $balance): ?>
                    <tr>
                        <td><?= e(strtoupper((string) $balance['kas_type'])) ?></td>
                        <td><?= !empty($balance['rt_kode']) ? 'RT ' . e((string) $balance['rt_kode']) : '-' ?></td>
                        <td class="fw-semibold text-primary"><?= rupiah((float) $balance['balance']) ?></td>
                        <td><?= e((string) ($balance['last_updated'] ?? '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($balances)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Saldo kas belum tersedia.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
