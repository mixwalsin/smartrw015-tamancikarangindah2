<?php

/**
 * PendudukModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class PendudukModel extends Model
{
    protected string $table = 'penduduk';

    public function findByNik(string $nik): array|false
    {
        return $this->findWhere('nik', $nik);
    }

    public function getByRt(string $rt): array
    {
        return $this->where('rt', $rt);
    }

    public function countByJenisKelamin(): array
    {
        return $this->query(
            "SELECT jenis_kelamin, COUNT(*) as total FROM {$this->table} GROUP BY jenis_kelamin"
        );
    }

    public function countByRt(): array
    {
        return $this->query(
            "SELECT rt, COUNT(*) as total FROM {$this->table} GROUP BY rt ORDER BY rt ASC"
        );
    }

    public function search(string $keyword): array
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE nik LIKE ? OR nama LIKE ? OR alamat LIKE ?",
            ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]
        );
    }

    public function paginateWithSearch(int $page, string $keyword = '', int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = '';
        $params = [];

        if ($keyword !== '') {
            $where  = "WHERE nik LIKE ? OR nama LIKE ? OR alamat LIKE ?";
            $params = ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"];
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} {$where} ORDER BY nama ASC LIMIT ? OFFSET ?"
        );

        $i = 1;
        foreach ($params as $v) {
            $stmt->bindValue($i++, $v);
        }
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
            'keyword'      => $keyword,
        ];
    }
}
