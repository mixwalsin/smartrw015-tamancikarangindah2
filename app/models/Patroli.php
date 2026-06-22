<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class Patroli extends Model
{
    protected string $table = 'patroli';

    public function latest(int $limit = 20): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY tanggal_patroli DESC, jam_patroli DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function summaryByLocation(int $days = 30): array
    {
        return $this->query(
            "SELECT lokasi_patroli, COUNT(*) AS total
             FROM {$this->table}
             WHERE tanggal_patroli >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY lokasi_patroli
             ORDER BY total DESC",
            [$days]
        );
    }
}
