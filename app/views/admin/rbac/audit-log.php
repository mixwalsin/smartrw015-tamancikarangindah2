<!-- Admin: Audit Log Aktivitas -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-journal-text me-2 text-secondary"></i>Audit Log Aktivitas</h4>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= url('admin/audit-log') ?>" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Modul</label>
                    <select name="modul" class="form-select form-select-sm">
                        <option value="">Semua Modul</option>
                        <?php foreach ($moduls as $m): ?>
                            <option value="<?= e($m) ?>" <?= $filters['modul'] === $m ? 'selected' : '' ?>>
                                <?= e($m) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Aksi</label>
                    <input type="text" name="aksi" class="form-control form-control-sm"
                           placeholder="login, create, update..."
                           value="<?= e($filters['aksi']) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="<?= url('admin/audit-log') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Log Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Modul</th>
                            <th>Data ID</th>
                            <th>Keterangan</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs['data'])): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada data log.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs['data'] as $log): ?>
                                <tr>
                                    <td class="small text-muted"><?= e($log['created_at']) ?></td>
                                    <td class="small">
                                        <?php if ($log['user_name']): ?>
                                            <span class="fw-medium"><?= e($log['user_name']) ?></span><br>
                                            <small class="text-muted"><?= e($log['username'] ?? '') ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Sistem</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $aksiClass = match ($log['aksi']) {
                                            'create', 'login'          => 'bg-success',
                                            'update', 'assign_role',
                                            'assign_permission'        => 'bg-warning text-dark',
                                            'delete'                   => 'bg-danger',
                                            'logout'                   => 'bg-secondary',
                                            default                    => 'bg-info',
                                        };
                                        ?>
                                        <span class="badge <?= $aksiClass ?>"><?= e($log['aksi']) ?></span>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= e($log['modul']) ?></span></td>
                                    <td class="small text-muted"><?= $log['data_id'] ?? '-' ?></td>
                                    <td class="small"><?= e(truncate($log['keterangan'] ?? '', 60)) ?></td>
                                    <td class="small text-muted"><?= e($log['ip_address'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (($logs['last_page'] ?? 1) > 1): ?>
            <div class="card-footer bg-white">
                <?= paginate($logs, 'admin/audit-log?modul=' . urlencode($filters['modul']) . '&aksi=' . urlencode($filters['aksi'])) ?>
            </div>
        <?php endif; ?>
    </div>

</div>
