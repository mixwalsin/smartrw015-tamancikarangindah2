<?php

/**
 * Admin\AuditLogController – Audit Trail Aktivitas
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

namespace Admin;

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/AuditLogModel.php';

class AuditLogController extends \Controller
{
    private \AuditLogModel $auditLog;

    public function __construct()
    {
        $this->auditLog = new \AuditLogModel();
    }

    public function index(): void
    {
        $this->requirePermission('log.read');

        $page    = max(1, (int) $this->query('page', 1));
        $filters = [
            'modul'   => $this->query('modul', ''),
            'aksi'    => $this->query('aksi', ''),
            'user_id' => $this->query('user_id', ''),
        ];

        $this->view('admin/rbac/audit-log', [
            'title'   => 'Audit Log Aktivitas - ' . APP_NAME,
            'logs'    => $this->auditLog->paginated($page, 20, $filters),
            'filters' => $filters,
            'moduls'  => $this->auditLog->getModuls(),
        ]);
    }
}
