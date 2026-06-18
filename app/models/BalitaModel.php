<?php

/**
 * BalitaModel
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class BalitaModel extends Model
{
    protected string $table = 'balita';

    public function search(string $keyword): array
    {
        return $this->query(
            "SELECT * FROM {$this->table}
             WHERE nama LIKE ? OR nama_ibu LIKE ? OR rt LIKE ?
             ORDER BY nama ASC",
            ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]
        );
    }

    public function paginateWithSearch(int $page, string $keyword = '', int $perPage = PAGINATION_LIMIT): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = '';
        $params = [];

        if ($keyword !== '') {
            $where  = "WHERE nama LIKE ? OR nama_ibu LIKE ? OR rt LIKE ?";
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

    public function countByStatusGizi(): array
    {
        return $this->query(
            "SELECT status_imunisasi, COUNT(*) as total
             FROM {$this->table}
             GROUP BY status_imunisasi"
        );
    }

    /**
     * Hitung umur balita dalam bulan dari tgl_lahir
     */
    public function getWithUmur(): array
    {
        return $this->query(
            "SELECT *,
                TIMESTAMPDIFF(MONTH, tgl_lahir, CURDATE()) AS umur_bulan
             FROM {$this->table}
             ORDER BY nama ASC"
        );
    }
}
