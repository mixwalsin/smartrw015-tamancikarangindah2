<?php
$meta = $report['meta'];
$series = $report['series'];
$labels = array_map(static fn($row) => $row['month_name'], $series);
$incomeData = array_map(static fn($row) => (float) $row['pemasukan'], $series);
$expenseData = array_map(static fn($row) => (float) $row['pengeluaran'], $series);
$cumulativeIncome = array_map(static fn($row) => (float) $row['kumulatif_pemasukan'], $series);
$cumulativeExpense = array_map(static fn($row) => (float) $row['kumulatif_pengeluaran'], $series);
$balanceLabels = array_map(static fn($row) => $row['period'], $report['balance_trend']);
$balanceValues = array_map(static fn($row) => (float) $row['balance'], $report['balance_trend']);
?>
<div class="container-fluid">
    <h4 class="fw-bold mb-3">Laporan Tahunan Kas <?= strtoupper((string) $meta['kasType']) ?> - <?= (int) $meta['year'] ?></h4>
    <div class="row g-3">
        <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Bar Pemasukan vs Pengeluaran</div><div class="card-body"><canvas id="incomeExpenseBar" height="220"></canvas></div></div></div>
        <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Trend Saldo Kas</div><div class="card-body"><canvas id="balanceTrendLine" height="220"></canvas></div></div></div>
        <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Line Kumulatif Pemasukan & Pengeluaran</div><div class="card-body"><canvas id="cumulativeLine" height="110"></canvas></div></div></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('incomeExpenseBar'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>,
        datasets: [
            { label: 'Pemasukan', data: <?= json_encode($incomeData) ?>, backgroundColor: '#198754' },
            { label: 'Pengeluaran', data: <?= json_encode($expenseData) ?>, backgroundColor: '#dc3545' }
        ]
    }
});

new Chart(document.getElementById('balanceTrendLine'), {
    type: 'line',
    data: {
        labels: <?= json_encode($balanceLabels, JSON_UNESCAPED_UNICODE) ?>,
        datasets: [{ label: 'Saldo', data: <?= json_encode($balanceValues) ?>, borderColor: '#0d6efd', tension: 0.3 }]
    }
});

new Chart(document.getElementById('cumulativeLine'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>,
        datasets: [
            { label: 'Kumulatif Pemasukan', data: <?= json_encode($cumulativeIncome) ?>, borderColor: '#198754', tension: 0.2 },
            { label: 'Kumulatif Pengeluaran', data: <?= json_encode($cumulativeExpense) ?>, borderColor: '#dc3545', tension: 0.2 }
        ]
    }
});
</script>
