<?php

/**
 * PendudukModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PendudukModel extends Model
{
    protected string $table = 'warga';

    public function findByNik(string $nik): array|false
    {
        return $this->findWhere('nik', $nik);
    }

    public function paginateWithSearch(int $page, string $keyword = '', int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];

        if ($keyword !== '') {
            $conditions[] = '(w.nik LIKE ? OR w.nama LIKE ? OR kk.alamat LIKE ? OR kk.no_kk LIKE ?)';
            $params = ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%", "%{$keyword}%"];
        }

        $whereSql = $conditions !== [] ? ' WHERE ' . implode(' AND ', $conditions) : '';

        $countStmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM warga w
             LEFT JOIN kk ON kk.id = w.kk_id
             LEFT JOIN rt ON rt.id = kk.rt_id' . $whereSql
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT w.*, kk.no_kk, kk.alamat, rt.kode AS rt
             FROM warga w
             LEFT JOIN kk ON kk.id = w.kk_id
             LEFT JOIN rt ON rt.id = kk.rt_id' . $whereSql . '
             ORDER BY w.nama ASC LIMIT ? OFFSET ?'
        );

        $i = 1;
        foreach ($params as $value) {
            $stmt->bindValue($i++, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
            'keyword'      => $keyword,
        ];
    }

    public function countByJenisKelamin(): array
    {
        return $this->query('SELECT jenis_kelamin, COUNT(*) as total FROM warga GROUP BY jenis_kelamin');
    }

    public function countByRt(): array
    {
        return $this->query(
            'SELECT rt.kode AS rt, COUNT(*) as total
             FROM warga w
             INNER JOIN kk ON kk.id = w.kk_id
             INNER JOIN rt ON rt.id = kk.rt_id
             GROUP BY rt.kode
             ORDER BY rt.kode ASC'
        );
    }

    public function findDetailed(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT w.*, kk.no_kk, kk.alamat, rt.kode AS rt, rw.kode AS rw
             FROM warga w
             LEFT JOIN kk ON kk.id = w.kk_id
             LEFT JOIN rt ON rt.id = kk.rt_id
             LEFT JOIN rw ON rw.id = rt.rw_id
             WHERE w.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function insertFromForm(array $data): string|false
    {
        $kkId = $this->resolveKkId($data);
        return $this->insert([
            'kk_id'         => $kkId,
            'nik'           => $data['nik'],
            'nama'          => $data['nama'],
            'tempat_lahir'  => $data['tempat_lahir'] ?: null,
            'tanggal_lahir' => $data['tanggal_lahir'] ?: null,
            'jenis_kelamin' => $data['jenis_kelamin'] ?: null,
            'agama'         => $data['agama'] ?: null,
            'pekerjaan'     => $data['pekerjaan'] ?: null,
            'status_kawin'  => $data['status_kawin'] ?: null,
        ]);
    }

    public function updateFromForm(int $id, array $data): bool
    {
        $kkId = $this->resolveKkId($data);
        return $this->update($id, [
            'kk_id'         => $kkId,
            'nama'          => $data['nama'],
            'tempat_lahir'  => $data['tempat_lahir'] ?: null,
            'tanggal_lahir' => $data['tanggal_lahir'] ?: null,
            'jenis_kelamin' => $data['jenis_kelamin'] ?: null,
            'agama'         => $data['agama'] ?: null,
            'pekerjaan'     => $data['pekerjaan'] ?: null,
            'status_kawin'  => $data['status_kawin'] ?: null,
        ]);
    }

    private function resolveKkId(array $data): int
    {
        $stmt = $this->db->prepare('SELECT id FROM kk WHERE no_kk = ? LIMIT 1');
        $stmt->execute([$data['no_kk']]);
        $existing = $stmt->fetchColumn();
        if ($existing) {
            $this->execute('UPDATE kk SET alamat = ?, updated_at = ? WHERE id = ?', [
                $data['alamat'],
                date('Y-m-d H:i:s'),
                $existing,
            ]);
            return (int) $existing;
        }

        $rtStmt = $this->db->prepare('SELECT id FROM rt WHERE kode = ? LIMIT 1');
        $rtStmt->execute([$data['rt']]);
        $rtId = (int) $rtStmt->fetchColumn();
        if ($rtId === 0) {
            throw new RuntimeException('RT tidak valid: ' . ($data['rt'] ?? '-') . '. Gunakan kode RT001 sampai RT007.');
        }

        $kkStmt = $this->db->prepare(
            'INSERT INTO kk (rt_id, no_kk, alamat, rt_text, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $now = date('Y-m-d H:i:s');
        $kkStmt->execute([$rtId, $data['no_kk'], $data['alamat'], $data['rt'], $now, $now]);
        return (int) $this->db->lastInsertId();
    }
}
