<?php

/**
 * WhatsAppGatewayClient
 */

declare(strict_types=1);

class WhatsAppGatewayClient
{
    public function sendMessage(string $recipient, string $message, array $metadata = []): array
    {
        $recipient = trim($recipient);
        $message = trim($message);

        if ($recipient === '' || $message === '') {
            return [
                'success' => false,
                'error' => 'Nomor tujuan dan pesan wajib diisi.',
            ];
        }

        if (!WA_GATEWAY_ENABLED) {
            return [
                'success' => false,
                'error' => 'WhatsApp Gateway nonaktif. Aktifkan WA_GATEWAY_ENABLED=true.',
            ];
        }

        if (WA_GATEWAY_ENDPOINT === '') {
            return [
                'success' => false,
                'error' => 'WA_GATEWAY_ENDPOINT belum dikonfigurasi.',
            ];
        }

        $payload = [
            WA_GATEWAY_RECIPIENT_FIELD => $recipient,
            WA_GATEWAY_MESSAGE_FIELD => $message,
        ];

        if ($metadata !== []) {
            $payload[WA_GATEWAY_METADATA_FIELD] = $metadata;
        }

        if (WA_GATEWAY_EXTRA_PAYLOAD !== []) {
            $payload = array_merge(WA_GATEWAY_EXTRA_PAYLOAD, $payload);
        }

        $headers = ['Content-Type: application/json'];
        if (WA_GATEWAY_API_KEY !== '') {
            $token = WA_GATEWAY_API_KEY;
            if (WA_GATEWAY_AUTH_SCHEME !== '') {
                $token = trim(WA_GATEWAY_AUTH_SCHEME) . ' ' . $token;
            }
            $headers[] = WA_GATEWAY_AUTH_HEADER . ': ' . trim($token);
        }

        $ch = curl_init(WA_GATEWAY_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => WA_GATEWAY_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $responseBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false) {
            return [
                'success' => false,
                'error' => 'Gagal menghubungi WhatsApp Gateway: ' . $curlError,
            ];
        }

        $decoded = json_decode($responseBody, true);
        if (!is_array($decoded)) {
            $decoded = ['raw' => $responseBody];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'status_code' => $httpCode,
            'response' => $decoded,
        ];
    }
}
