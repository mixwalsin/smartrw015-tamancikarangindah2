<?php

declare(strict_types=1);

abstract class SimpleCrudController extends Controller
{
    protected GenericTableModel $model;
    protected array $config = [];

    public function __construct()
    {
        $this->boot();
        $this->model = new GenericTableModel($this->config['table']);
    }

    abstract protected function boot(): void;

    public function index(): void
    {
        $this->authorize('index');
        $page    = max(1, (int) $this->query('page', 1));
        $keyword = trim((string) $this->query('keyword', ''));

        $pagination = $this->model->paginateQuery(
            $this->config['select_sql'],
            $this->config['count_sql'],
            $page,
            $keyword,
            $this->config['search_columns'] ?? []
        );

        $this->renderModuleIndex([
            'title'       => $this->config['title'],
            'icon'        => $this->config['icon'] ?? 'bi-grid',
            'routeBase'   => $this->config['route'],
            'columns'     => $this->config['columns'],
            'pagination'  => $pagination,
            'keyword'     => $keyword,
            'canCreate'   => $this->can('create'),
            'canEdit'     => $this->can('edit'),
            'canDelete'   => $this->can('delete'),
            'canShow'     => true,
            'extraButtons'=> $this->config['extra_buttons'] ?? [],
            'subtitle'    => $this->config['subtitle'] ?? null,
        ]);
    }

    public function create(): void
    {
        $this->authorize('create');
        $this->renderModuleForm([
            'title'      => 'Tambah ' . $this->config['title'],
            'icon'       => $this->config['icon'] ?? 'bi-plus-circle',
            'routeBase'  => $this->config['route'],
            'actionUrl'  => url($this->config['route'] . '/store'),
            'fields'     => $this->buildFields(),
            'submitText' => 'Simpan',
        ]);
    }

    public function store(): void
    {
        $this->authorize('create');
        $this->verifyCsrfOrRedirect('create');

        $data = $this->sanitizePostedData();
        $this->validateRequired($data);
        $data = $this->mutateBeforeSave($data, null);

        $id = $this->model->insert($data);
        logActivity('create', $this->config['table'], (int) $id, 'Menambah data ' . $this->config['title']);
        setFlash('success', $this->config['title'] . ' berhasil ditambahkan.');
        $this->redirect($this->config['route']);
    }

    public function show(string $id): void
    {
        $this->authorize('index');
        $row = $this->findOne((int) $id);
        if (!$row) {
            $this->redirect($this->config['route']);
        }

        $this->renderModuleShow([
            'title'     => 'Detail ' . $this->config['title'],
            'icon'      => $this->config['icon'] ?? 'bi-eye',
            'routeBase' => $this->config['route'],
            'row'       => $row,
            'columns'   => $this->config['columns'],
        ]);
    }

    public function edit(string $id): void
    {
        $this->authorize('edit');
        $row = $this->findOne((int) $id);
        if (!$row) {
            $this->redirect($this->config['route']);
        }

        $this->renderModuleForm([
            'title'      => 'Edit ' . $this->config['title'],
            'icon'       => $this->config['icon'] ?? 'bi-pencil',
            'routeBase'  => $this->config['route'],
            'actionUrl'  => url($this->config['route'] . '/update/' . $id),
            'fields'     => $this->buildFields($row),
            'submitText' => 'Perbarui',
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize('edit');
        $this->verifyCsrfOrRedirect('edit/' . $id);

        $data = $this->sanitizePostedData();
        $this->validateRequired($data);
        $data = $this->mutateBeforeSave($data, (int) $id);

        $this->model->update((int) $id, $data);
        logActivity('update', $this->config['table'], (int) $id, 'Memperbarui data ' . $this->config['title']);
        setFlash('success', $this->config['title'] . ' berhasil diperbarui.');
        $this->redirect($this->config['route']);
    }

    public function delete(string $id): void
    {
        $this->authorize('delete');
        $this->verifyCsrfOrRedirect('');

        $this->model->delete((int) $id);
        logActivity('delete', $this->config['table'], (int) $id, 'Menghapus data ' . $this->config['title']);
        setFlash('success', $this->config['title'] . ' berhasil dihapus.');
        $this->redirect($this->config['route']);
    }

    protected function authorize(string $action): void
    {
        $this->requireAuth();
        $roles = $this->config['roles'][$action] ?? $this->config['roles']['index'] ?? [];
        if ($roles !== []) {
            $this->requireRole(...$roles);
        }
    }

    protected function can(string $action): bool
    {
        $roles = $this->config['roles'][$action] ?? [];
        if ($roles === []) {
            return false;
        }

        return in_array(authUser()['role'] ?? '', $roles, true);
    }

    protected function buildFields(array $row = []): array
    {
        $fields = $this->config['fields'];
        foreach ($fields as $key => &$field) {
            $field['value'] = $row[$key] ?? $_POST[$key] ?? ($field['default'] ?? '');
            if (($field['type'] ?? 'text') === 'select' && isset($field['options_callback'])) {
                $field['options'] = $this->{$field['options_callback']}();
            }
        }
        unset($field);

        return $fields;
    }

    protected function sanitizePostedData(): array
    {
        $data = [];
        foreach ($this->config['fields'] as $key => $field) {
            $value = $this->input($key, '');
            if (is_string($value)) {
                $value = trim($value);
            }
            $data[$key] = $value === '' ? null : $value;
        }

        return $data;
    }

    protected function validateRequired(array $data): void
    {
        foreach ($this->config['fields'] as $key => $field) {
            if (($field['required'] ?? false) && empty($data[$key])) {
                setFlash('error', ($field['label'] ?? $key) . ' wajib diisi.');
                $this->redirect($this->config['route'] . '/create');
            }
        }
    }

    protected function mutateBeforeSave(array $data, ?int $id): array
    {
        return $data;
    }

    protected function verifyCsrfOrRedirect(string $path): void
    {
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $target = $path === '' ? $this->config['route'] : $this->config['route'] . '/' . ltrim($path, '/');
            $this->redirect($target);
        }
    }

    protected function findOne(int $id): array|false
    {
        // All select_sql/show_sql configs must use `base` as the primary table alias.
        $sql = ($this->config['show_sql'] ?? $this->config['select_sql']) . ' WHERE base.id = :id';
        return $this->model->fetchOneQuery($sql, ['id' => $id]);
    }

    protected function getRtOptions(): array
    {
        return $this->model->fetchPairs('SELECT id, CONCAT("RT ", kode) as label FROM rt ORDER BY kode ASC');
    }

    protected function getRoleOptions(): array
    {
        return $this->model->fetchPairs('SELECT id, name as label FROM roles ORDER BY id ASC');
    }
}
