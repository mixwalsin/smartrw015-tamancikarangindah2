<?php

/**
 * Konfigurasi WhatsApp Gateway
 *
 * Gunakan environment variable agar endpoint dan credential mudah diganti.
 */

declare(strict_types=1);

$waEnabled = getenv('WA_GATEWAY_ENABLED');
define('WA_GATEWAY_ENABLED', filter_var($waEnabled !== false ? $waEnabled : 'false', FILTER_VALIDATE_BOOLEAN));

define('WA_GATEWAY_ENDPOINT', (string) (getenv('WA_GATEWAY_ENDPOINT') ?: ''));
define('WA_GATEWAY_TIMEOUT', (int) (getenv('WA_GATEWAY_TIMEOUT') ?: 15));
define('WA_GATEWAY_CONNECT_TIMEOUT', (int) (getenv('WA_GATEWAY_CONNECT_TIMEOUT') ?: 5));
define('WA_GATEWAY_AUTH_HEADER', (string) (getenv('WA_GATEWAY_AUTH_HEADER') ?: 'Authorization'));
define('WA_GATEWAY_AUTH_SCHEME', (string) (getenv('WA_GATEWAY_AUTH_SCHEME') ?: 'Bearer'));
define('WA_GATEWAY_API_KEY', (string) (getenv('WA_GATEWAY_API_KEY') ?: ''));

define('WA_GATEWAY_RECIPIENT_FIELD', (string) (getenv('WA_GATEWAY_RECIPIENT_FIELD') ?: 'phone'));
define('WA_GATEWAY_MESSAGE_FIELD', (string) (getenv('WA_GATEWAY_MESSAGE_FIELD') ?: 'message'));
define('WA_GATEWAY_METADATA_FIELD', (string) (getenv('WA_GATEWAY_METADATA_FIELD') ?: 'metadata'));

define('WA_NOTIFICATION_API_KEY', (string) (getenv('WA_NOTIFICATION_API_KEY') ?: ''));

$waExtraPayload = [];
$waExtraPayloadRaw = getenv('WA_GATEWAY_EXTRA_PAYLOAD');
if ($waExtraPayloadRaw !== false && $waExtraPayloadRaw !== '') {
    $decoded = json_decode($waExtraPayloadRaw, true);
    if (is_array($decoded)) {
        $waExtraPayload = $decoded;
    } else {
        trigger_error('WA_GATEWAY_EXTRA_PAYLOAD bukan JSON object yang valid.', E_USER_WARNING);
    }
}
define('WA_GATEWAY_EXTRA_PAYLOAD', $waExtraPayload);
