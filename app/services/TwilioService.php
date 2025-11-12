<?php

namespace App\Services;

use RuntimeException;

class TwilioService
{
    private string $accountSid;
    private string $authToken;
    private ?string $whatsAppFrom;
    private ?string $smsFrom;
    private string $defaultCountryCode;

    public function __construct(
        ?string $accountSid = null,
        ?string $authToken = null,
        ?string $whatsAppFrom = null,
        ?string $smsFrom = null,
        ?string $defaultCountryCode = null
    ) {
        $this->accountSid = trim((string) ($accountSid ?? getenv('TWILIO_ACCOUNT_SID') ?: ''));
        $this->authToken = trim((string) ($authToken ?? getenv('TWILIO_AUTH_TOKEN') ?: ''));
        $this->whatsAppFrom = $this->sanitizeNumber($whatsAppFrom ?? getenv('TWILIO_WHATSAPP_FROM') ?: null);
        $this->smsFrom = $this->sanitizeNumber($smsFrom ?? getenv('TWILIO_SMS_FROM') ?: null);
        $code = $defaultCountryCode ?? getenv('TWILIO_DEFAULT_COUNTRY_CODE') ?: '+57';
        $code = trim((string) $code);
        if ($code === '') {
            $code = '+57';
        }
        if ($code[0] !== '+') {
            $code = '+' . ltrim($code, '+');
        }
        $this->defaultCountryCode = $code;
    }

    public function configured(): bool
    {
        return $this->accountSid !== '' && $this->authToken !== '';
    }

    public function sendWhatsApp(string $to, string $body, array $mediaUrls = []): array
    {
        $payload = [
            'From' => $this->formatFrom('whatsapp'),
            'To' => $this->formatDestination($to, 'whatsapp'),
            'Body' => $body,
        ];

        if (!empty($mediaUrls)) {
            $mediaUrls = array_values(array_filter($mediaUrls, static fn ($value) => is_string($value) && $value !== ''));
            if ($mediaUrls) {
                // Twilio admite múltiples MediaUrl repitiendo el parámetro. Utilizamos el primero disponible.
                $payload['MediaUrl'] = $mediaUrls[0];
            }
        }

        return $this->request('POST', '/Messages.json', $payload);
    }

    public function sendSms(string $to, string $body): array
    {
        $payload = [
            'From' => $this->formatFrom('sms'),
            'To' => $this->formatDestination($to, 'sms'),
            'Body' => $body,
        ];

        return $this->request('POST', '/Messages.json', $payload);
    }

    public function downloadMedia(string $url): array
    {
        $this->ensureCredentials();
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->accountSid . ':' . $this->authToken);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $content = curl_exec($ch);
        if ($content === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('No fue posible descargar el adjunto de Twilio: ' . $error);
        }

        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'application/octet-stream';
        curl_close($ch);

        if ($status >= 400) {
            throw new RuntimeException('Twilio retornó un error al descargar el adjunto. Código: ' . $status);
        }

        return [
            'content' => $content,
            'content_type' => $type,
            'size' => strlen($content),
        ];
    }

    private function sanitizeNumber(?string $number): ?string
    {
        $number = trim((string) $number);
        if ($number === '') {
            return null;
        }

        $number = preg_replace('/[^0-9+]/', '', $number);
        if ($number === '') {
            return null;
        }

        if ($number[0] !== '+') {
            $number = '+' . ltrim($number, '+');
        }

        return $number;
    }

    private function formatFrom(string $channel): string
    {
        if ($channel === 'whatsapp') {
            if (!$this->whatsAppFrom) {
                throw new RuntimeException('Configura el número remitente de WhatsApp en las variables de entorno de Twilio.');
            }

            return 'whatsapp:' . $this->whatsAppFrom;
        }

        if (!$this->smsFrom) {
            throw new RuntimeException('Configura el número remitente SMS en las variables de entorno de Twilio.');
        }

        return $this->smsFrom;
    }

    private function formatDestination(string $number, string $channel): string
    {
        $normalized = preg_replace('/\D+/', '', $number);
        if ($normalized === '') {
            throw new RuntimeException('El número de destino no es válido.');
        }

        if ($number !== '' && $number[0] === '+') {
            $formatted = '+' . ltrim($normalized, '+');
        } else {
            $formatted = $this->defaultCountryCode . ltrim($normalized, '0');
            if ($formatted[0] !== '+') {
                $formatted = '+' . ltrim($formatted, '+');
            }
        }

        if ($channel === 'whatsapp') {
            return 'whatsapp:' . $formatted;
        }

        return $formatted;
    }

    private function ensureCredentials(): void
    {
        if (!$this->configured()) {
            throw new RuntimeException('No se han configurado las credenciales de Twilio.');
        }
    }

    private function request(string $method, string $uri, array $data = []): array
    {
        $this->ensureCredentials();
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode($this->accountSid) . $uri;
        if ($method === 'GET' && $data) {
            $url .= '?' . http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->accountSid . ':' . $this->authToken);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&', PHP_QUERY_RFC3986));
        }

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Error al comunicarse con Twilio: ' . $error);
        }

        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Respuesta no válida de Twilio.');
        }

        if ($status >= 400) {
            $message = $decoded['message'] ?? 'Error desconocido en Twilio.';
            throw new RuntimeException('Twilio respondió con error: ' . $message);
        }

        return $decoded;
    }
}
