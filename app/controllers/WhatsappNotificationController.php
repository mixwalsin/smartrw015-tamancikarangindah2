<?php

/**
 * WhatsappNotificationController
 * API trigger notifikasi WhatsApp.
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/helpers/WhatsAppNotificationService.php';

class WhatsappNotificationController extends Controller
{
    private WhatsAppNotificationService $service;

    public function __construct()
    {
        $this->service = new WhatsAppNotificationService();
    }

    public function pengajuanSurat(): void
    {
        $this->dispatch('pengajuan_surat');
    }

    public function approvalSurat(): void
    {
        $this->dispatch('approval_surat');
    }

    public function pengumuman(): void
    {
        $this->dispatch('pengumuman');
    }

    public function iuranBulanan(): void
    {
        $this->dispatch('iuran_bulanan');
    }

    public function jadwalKegiatan(): void
    {
        $this->dispatch('jadwal_kegiatan');
    }

    private function dispatch(string $type): void
    {
        if (!$this->authorizeRequest()) {
            $this->json([
                'success' => false,
                'message' => 'Akses tidak diizinkan.',
            ], 401);
            return;
        }

        $payload = $this->getPayload();
        $phone = trim((string) ($payload['phone'] ?? $payload['no_hp'] ?? ''));

        if ($phone === '') {
            $this->json([
                'success' => false,
                'message' => 'Field phone/no_hp wajib diisi.',
            ], 422);
            return;
        }

        $data = $payload['data'] ?? [];
        if (!is_array($data)) {
            $data = [];
        }

        switch ($type) {
            case 'pengajuan_surat':
                $result = $this->service->notifyPengajuanSurat($phone, $data);
                break;
            case 'approval_surat':
                $result = $this->service->notifyApprovalSurat($phone, $data);
                break;
            case 'pengumuman':
                $result = $this->service->notifyPengumuman($phone, $data);
                break;
            case 'iuran_bulanan':
                $result = $this->service->notifyIuranBulanan($phone, $data);
                break;
            case 'jadwal_kegiatan':
                $result = $this->service->notifyJadwalKegiatan($phone, $data);
                break;
            default:
                $this->json([
                    'success' => false,
                    'message' => 'Jenis notifikasi tidak didukung.',
                ], 400);
                return;
        }

        $statusCode = ($result['success'] ?? false) ? 200 : 500;
        $this->json($result, $statusCode);
    }

    private function authorizeRequest(): bool
    {
        if (WA_NOTIFICATION_API_KEY === '') {
            return isLoggedIn();
        }

        $provided = $this->getHeader('X-Notification-Key');
        return $provided !== '' && hash_equals(WA_NOTIFICATION_API_KEY, $provided);
    }

    private function getPayload(): array
    {
        $payload = $_POST;
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw ?: '[]', true);
            if (($raw ?? '') !== '' && json_last_error() !== JSON_ERROR_NONE) {
                $this->json([
                    'success' => false,
                    'message' => 'Format JSON tidak valid.',
                ], 400);
            }

            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        return is_array($payload) ? $payload : [];
    }

    private function getHeader(string $headerName): string
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $headerName));
        return trim((string) ($_SERVER[$serverKey] ?? ''));
    }
}
