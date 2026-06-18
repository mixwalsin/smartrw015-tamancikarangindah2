<!-- Admin: Daftar Permission -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-key me-2 text-success"></i>Manajemen Permission</h4>
        <?php if (can('permission.create')): ?>
            <a href="<?= url('admin/permissions/create') ?>" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah Permission
            </a>
        <?php endif; ?>
    </div>

    <!-- Filter per modul -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <button class="btn btn-sm btn-primary filter-btn active" data-modul="all">Semua</button>
        <?php foreach ($moduls as $m): ?>
            <button class="btn btn-sm btn-outline-secondary filter-btn" data-modul="<?= e($m) ?>">
                <?= e($m) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="permTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Modul</th>
                            <th>Roles</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permissions as $perm): ?>
                            <tr data-modul="<?= e($perm['modul']) ?>">
                                <td><?= (int) $perm['id'] ?></td>
                                <td class="fw-semibold small"><?= e($perm['name']) ?></td>
                                <td><code><?= e($perm['slug']) ?></code></td>
                                <td><span class="badge bg-secondary"><?= e($perm['modul']) ?></span></td>
                                <td class="text-muted small"><?= e($perm['roles'] ?? '-') ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if (can('permission.update')): ?>
                                            <a href="<?= url('admin/permissions/edit/' . $perm['id']) ?>"
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (can('permission.delete')): ?>
                                            <form method="POST"
                                                  action="<?= url('admin/permissions/delete/' . $perm['id']) ?>"
                                                  onsubmit="return confirm('Hapus permission <?= e($perm['slug']) ?>?')">
                                                <?= csrfField() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
document.querySelectorAll('.filter-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.add('btn-outline-secondary'));
        this.classList.add('active', 'btn-primary');
        this.classList.remove('btn-outline-secondary');

        const modul = this.dataset.modul;
        document.querySelectorAll('#permTable tbody tr').forEach(function (row) {
            row.style.display = (modul === 'all' || row.dataset.modul === modul) ? '' : 'none';
        });
    });
});
</script>
