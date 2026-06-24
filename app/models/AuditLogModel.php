<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class AuditLogModel extends Model
{
    protected string $table = 'log_aktivitas';
}
