<?php

/**
 * PengajuanSuratModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/models/SuratHistoryModel.php';

class PengajuanSuratModel extends Model
{
    protected string $table = 'surat_pengajuan';

    // ──────────────────────────────────────────
    // Status Constants
    // ──────────────────────────────────────────

    public const STATUS_DRAFT        = 'draft';
    public const STATUS_MENUNGGU_RT  = 'menunggu_rt';
    public const STATUS_MENUNGGU_RW  = 'menunggu_rw';
    public const STATUS_DISETUJUI    = 'disetujui';
    public const STATUS_DITOLAK      = 'ditolak';
    public const STATUS_SELESAI      = 'selesai';

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT        => 'Draft',
            self::STATUS_MENUNGGU_RT  => 'Menunggu RT',
            self::STATUS_MENUNGGU_RW  => 'Menunggu RW',
            self::STATUS_DISETUJUI    => 'Disetujui',
            self::STATUS_DITOLAK      => 'Ditolak',
            self::STATUS_SELESAI      => 'Selesai',
            default                   => ucfirst($status),
        };
    }

    public static function statusBadge(string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT        => 'secondary',
            self::STATUS_MENUNGGU_RT  => 'warning',
            self::STATUS_MENUNGGU_RW  => 'info',
            self::STATUS_DISETUJUI    => 'success',
            self::STATUS_DITOLAK      => 'danger',
            self::STATUS_SELESAI      => 'primary',
            default                   => 'secondary',
        };
    }

    // ──────────────────────────────────────────
    // Query Methods
    // ──────────────────────────────────────────

    /**
     * Get all pengajuan with jenis surat info, paginated
     */
    public function paginateWithFilter(
        int    $page,
        string $status   = '',
        string $keyword  = '',
        int    $dibuatOleh = 0,
        string $rt       = '',
        int    $perPage  = PAGINATION_LIMIT
    ): array {
        $offset = ($page - 1) * $perPage;
        $wheres = [];
        $params = [];

        if ($status !== '') {
            $wheres[] = 'sp.status = ?';
            $params[] = $status;
        }
        if ($dibuatOleh > 0) {
            $wheres[] = 'sp.dibuat_oleh = ?';
            $params[] = $dibuatOleh;
        }
        if ($rt !== '') {
            $wheres[] = 'sp.pemohon_rt = ?';
            $params[] = $rt;
        }
        if ($keyword !== '') {
            $wheres[] = '(sp.pemohon_nama LIKE ? OR sp.pemohon_nik LIKE ? OR sp.no_surat LIKE ?)';
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }

        $where = $wheres ? 'WHERE ' . implode(' AND ', $wheres) : '';

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} sp {$where}"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT sp.*, sj.nama AS jenis_nama, sj.kode AS jenis_kode,
                    u.name AS dibuat_nama
             FROM {$this->table} sp
             JOIN surat_jenis sj ON sj.id = sp.jenis_id
             JOIN users u        ON u.id  = sp.dibuat_oleh
             {$where}
             ORDER BY sp.created_at DESC
             LIMIT ? OFFSET ?"
        );

        $i = 1;
        foreach ($params as $v) {
            $stmt->bindValue($i++, $v);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i,   $offset,  PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
            'keyword'      => $keyword,
            'status'       => $status,
        ];
    }

    /**
     * Find with jenis surat info by ID
     */
    public function findWithDetail(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT sp.*,
                    sj.nama         AS jenis_nama,
                    sj.kode         AS jenis_kode,
                    sj.template_isi AS jenis_template,
                    sj.syarat       AS jenis_syarat,
                    u.name          AS dibuat_nama,
                    u.username      AS dibuat_username,
                    urt.name        AS rt_verifikasi_nama,
                    urw.name        AS rw_approval_nama
             FROM {$this->table} sp
             JOIN surat_jenis sj   ON sj.id  = sp.jenis_id
             JOIN users u          ON u.id   = sp.dibuat_oleh
             LEFT JOIN users urt   ON urt.id = sp.rt_verifikasi_oleh
             LEFT JOIN users urw   ON urw.id = sp.rw_approval_oleh
             WHERE sp.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Find by kode_verifikasi (for QR scan)
     */
    public function findByKodeVerifikasi(string $kode): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT sp.*,
                    sj.nama AS jenis_nama,
                    sj.kode AS jenis_kode,
                    u.name  AS dibuat_nama
             FROM {$this->table} sp
             JOIN surat_jenis sj ON sj.id = sp.jenis_id
             JOIN users u        ON u.id  = sp.dibuat_oleh
             WHERE sp.kode_verifikasi = ?
             LIMIT 1"
        );
        $stmt->execute([$kode]);
        return $stmt->fetch();
    }

    /**
     * Count by status (for dashboard)
     */
    public function countByStatus(): array
    {
        $rows = $this->query(
            "SELECT status, COUNT(*) AS total FROM {$this->table} GROUP BY status"
        );
        $result = [];
        foreach ($rows as $row) {
            $result[$row['status']] = (int) $row['total'];
        }
        return $result;
    }

    /**
     * Count pending for RT (menunggu_rt and RT from a certain RT number)
     */
    public function countPendingRt(string $rt = ''): int
    {
        $sql    = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'menunggu_rt'";
        $params = [];
        if ($rt !== '') {
            $sql    .= ' AND pemohon_rt = ?';
            $params[] = $rt;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Count pending for RW approval
     */
    public function countPendingRw(): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE status = 'menunggu_rw'"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Generate unique verification code
     */
    public function generateKodeVerifikasi(): string
    {
        do {
            $kode = strtoupper(bin2hex(random_bytes(8)));
        } while ($this->findWhere('kode_verifikasi', $kode));
        return $kode;
    }

    /**
     * Generate nomor surat: XXX/RW015/KODE/MM/YYYY
     */
    public function generateNoSurat(string $kodeJenis): string
    {
        $year  = date('Y');
        $month = date('m');
        $stmt  = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE status IN ('disetujui','selesai')
               AND YEAR(updated_at) = ? AND MONTH(updated_at) = ?"
        );
        $stmt->execute([$year, $month]);
        $seq = ((int) $stmt->fetchColumn()) + 1;

        return sprintf('%03d/RW015/%s/%s/%s', $seq, $kodeJenis, $month, $year);
    }

    /**
     * Update status with audit trail
     */
    public function updateStatus(
        int    $id,
        string $newStatus,
        int    $userId,
        string $catatan    = '',
        array  $extraData  = []
    ): bool {
        $row = $this->find($id);
        if (!$row) {
            return false;
        }

        $updateData = ['status' => $newStatus] + $extraData;
        $updated    = $this->update($id, $updateData);

        if ($updated) {
            $historyModel = new SuratHistoryModel();
            $historyModel->insert([
                'pengajuan_id'   => $id,
                'status_lama'    => $row['status'],
                'status_baru'    => $newStatus,
                'catatan'        => $catatan,
                'dilakukan_oleh' => $userId,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        return $updated;
    }

    /**
     * Get recent pengajuan for dashboard
     */
    public function recent(int $limit = 5): array
    {
        return $this->query(
            "SELECT sp.*, sj.nama AS jenis_nama, u.name AS dibuat_nama
             FROM {$this->table} sp
             JOIN surat_jenis sj ON sj.id = sp.jenis_id
             JOIN users u        ON u.id  = sp.dibuat_oleh
             ORDER BY sp.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    // Override addTimestamps-style insert to skip updated_at for history
    public function insertRaw(array $data): string|false
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }
}
