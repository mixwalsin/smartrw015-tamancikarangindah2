<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Analytics Pengaduan</h4>
            <div class="text-muted small">Ringkasan performa penanganan, kategori dominan, dan tren pengaduan.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('pengaduan/export/excel') ?>" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
            <a href="<?= url('pengaduan') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total</div><div class="fs-3 fw-bold"><?= number_format($report['summary']['total'] ?? 0) ?></div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Diproses</div><div class="fs-3 fw-bold text-warning"><?= number_format($report['summary']['diproses'] ?? 0) ?></div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Selesai</div><div class="fs-3 fw-bold text-success"><?= number_format($report['summary']['selesai'] ?? 0) ?></div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Ditolak</div><div class="fs-3 fw-bold text-danger"><?= number_format($report['summary']['ditolak'] ?? 0) ?></div></div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Total Pengaduan per Kategori</div>
                <div class="card-body">
                    <canvas id="kategoriChart" height="240"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Trend Pengaduan per Bulan</div>
                <div class="card-body">
                    <canvas id="trendChart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const categories = <?= json_encode($report['categories'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
const trend = <?= json_encode($report['trend'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
new Chart(document.getElementById('kategoriChart'), {
    type: 'doughnut',
    data: {
        labels: categories.map(item => item.label),
        datasets: [{
            data: categories.map(item => Number(item.total)),
            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997']
        }]
    }
});
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trend.map(item => item.bulan),
        datasets: [{
            label: 'Jumlah Pengaduan',
            data: trend.map(item => Number(item.total)),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.15)',
            fill: true,
            tension: 0.3,
        }]
    }
});
</script>
