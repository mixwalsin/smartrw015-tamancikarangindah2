<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class GatePass extends Model
{
    protected string $table = 'gate_pass';

    public function generatePayload(string $nama, string $nomorId): string
    {
        return base64_encode(json_encode([
            'nama' => $nama,
            'id' => $nomorId,
            'ts' => time(),
            'token' => bin2hex(random_bytes(8)),
        ], JSON_UNESCAPED_UNICODE));
    }

    public function activeByCode(string $qrCode): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE qr_code = ? AND status = 'aktif'
               AND tanggal_berlaku >= CURDATE()"
        );
        $stmt->execute([$qrCode]);
        return $stmt->fetch();
    }

    public function expire(int $id): bool
    {
        return $this->execute("UPDATE {$this->table} SET status = 'expired', updated_at = NOW() WHERE id = ?", [$id]);
    }
}
