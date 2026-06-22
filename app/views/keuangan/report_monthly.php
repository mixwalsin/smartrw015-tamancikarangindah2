<?php
$meta = $report['meta'];
$expenseByCategory = $report['expense_by_category'] ?? [];
$categoryLabels = array_map(static fn($row) => $row['name'], $expenseByCategory);
$categoryValues = array_map(static fn($row) => (float) $row['total'], $expenseByCategory);
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Laporan Bulanan Kas <?= strtoupper((string) $meta['kasType']) ?></h4>
        <div class="d-flex gap-2">
            <a href="<?= url('keuangan/report/export-pdf?kas_type=' . $meta['kasType'] . '&year=' . $meta['year'] . '&month=' . $meta['month']) ?>" class="btn btn-outline-danger btn-sm">Export PDF</a>
            <a href="<?= url('keuangan/report/export-excel?kas_type=' . $meta['kasType'] . '&year=' . $meta['year'] . '&month=' . $meta['month']) ?>" class="btn btn-outline-success btn-sm">Export Excel</a>
            <a href="<?= url('keuangan/report/print?kas_type=' . $meta['kasType'] . '&year=' . $meta['year'] . '&month=' . $meta['month']) ?>" class="btn btn-outline-primary btn-sm">Print</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Pemasukan</div><div class="fw-bold text-success fs-5"><?= rupiah((float) $report['total_pemasukan']) ?></div></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Pengeluaran</div><div class="fw-bold text-danger fs-5"><?= rupiah((float) $report['total_pengeluaran']) ?></div></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Saldo</div><div class="fw-bold text-primary fs-5"><?= rupiah((float) $report['saldo']) ?></div></div></div></div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Pie Pengeluaran per Kategori</div><div class="card-body"><canvas id="expenseCategoryPie" height="220"></canvas></div></div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Transaksi Bulanan</div>
                <div class="table-responsive"><table class="table table-sm mb-0"><thead class="table-light"><tr><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Nominal</th></tr></thead><tbody>
                        <?php foreach ($report['items'] as $item): ?>
                            <tr>
                                <td><?= formatDate((string) $item['date'], 'd/m/Y') ?></td>
                                <td><?= e((string) $item['transaction_type']) ?></td>
                                <td><?= e((string) ($item['category_name'] ?? '-')) ?></td>
                                <td><?= rupiah((float) $item['amount']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($report['items'])): ?><tr><td colspan="4" class="text-center text-muted py-3">Belum ada data.</td></tr><?php endif; ?>
                    </tbody></table></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('expenseCategoryPie'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($categoryLabels, JSON_UNESCAPED_UNICODE) ?>,
        datasets: [{ data: <?= json_encode($categoryValues) ?>, backgroundColor: ['#0d6efd','#198754','#dc3545','#ffc107','#20c997','#6f42c1'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
