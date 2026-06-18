<!-- Admin: Daftar Role -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Manajemen Role</h4>
        <?php if (can('role.create')): ?>
            <a href="<?= url('admin/roles/create') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah Role
            </a>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Role</th>
                            <th>Slug</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Pengguna</th>
                            <th class="text-center">Permissions</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><?= (int) $role['id'] ?></td>
                                <td class="fw-semibold"><?= e($role['name']) ?></td>
                                <td><code><?= e($role['slug']) ?></code></td>
                                <td class="text-muted small"><?= e($role['description'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= (int) $role['total_users'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= (int) $role['total_permissions'] ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <?php if (can('role.assign_permission')): ?>
                                            <a href="<?= url('admin/roles/permissions/' . $role['id']) ?>"
                                               class="btn btn-sm btn-outline-info"
                                               title="Kelola Permission">
                                                <i class="bi bi-key"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (can('role.update')): ?>
                                            <a href="<?= url('admin/roles/edit/' . $role['id']) ?>"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (can('role.delete') && (int) $role['id'] !== 1): ?>
                                            <form method="POST"
                                                  action="<?= url('admin/roles/delete/' . $role['id']) ?>"
                                                  onsubmit="return confirm('Hapus role <?= e($role['name']) ?>?')">
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
