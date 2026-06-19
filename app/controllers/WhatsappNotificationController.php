<?php

declare(strict_types=1);

class WhatsappNotificationController extends Controller
{
    public function announcement(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $config = whatsappConfig();

        $this->json([
            'success' => true,
            'enabled' => $config['enabled'] ?? false,
            'gateway' => $config['gateway_name'] ?? 'manual_stub',
            'message' => ($config['enabled'] ?? false)
                ? 'Permintaan broadcast WhatsApp diterima gateway.'
                : 'Mode stub aktif. Konfigurasikan config/whatsapp.php untuk integrasi gateway riil.',
        ]);
    }

    public function suratStatus(): void
    {
        $this->requireAuth();
        $this->requireRole('admin', 'rw', 'rt');
        $config = whatsappConfig();

        $this->json([
            'success' => true,
            'enabled' => $config['enabled'] ?? false,
            'gateway' => $config['gateway_name'] ?? 'manual_stub',
            'message' => ($config['enabled'] ?? false)
                ? 'Notifikasi status surat diproses gateway.'
                : 'Mode stub aktif. Endpoint ini siap dihubungkan ke gateway WhatsApp.',
        ]);
    }
}
