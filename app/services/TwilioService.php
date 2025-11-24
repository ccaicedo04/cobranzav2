<?php

namespace App\Services;

use RuntimeException;
use Twilio\Rest\Client;

class TwilioService
{
    private string $accountSid;
    private string $authToken;
    private ?string $whatsAppFrom;
    private ?string $smsFrom;
    private string $defaultCountryCode;
    private ?string $statusCallback;
    private ?Client $client = null;

    public function __construct(
        ?string $accountSid = null,
        ?string $authToken = null,
        ?string $whatsAppFrom = null,
        ?string $smsFrom = null,
        ?string $defaultCountryCode = null,
        ?string $statusCallback = null
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
        $statusCallback = trim((string) ($statusCallback ?? getenv('TWILIO_STATUS_CALLBACK') ?: ''));
        $this->statusCallback = $statusCallback !== '' ? $statusCallback : null;

        if ($this->configured() && class_exists(Client::class)) {
            $this->client = new Client($this->accountSid, $this->authToken);
        }
    }

    public function configured(): bool
    {
        return $this->accountSid !== '' && $this->authToken !== '';
    }

    public function ready(): bool
    {
        return $this->configured() && class_exists(Client::class);
    }

    public function sendWhatsApp(string $to, string $body, array $mediaUrls = []): array
    {
        $this->ensureClient();

        $options = [
            'from' => $this->formatFrom('whatsapp'),
            'body' => $body,
        ];

        if (!empty($mediaUrls)) {
            $mediaUrls = array_values(array_filter($mediaUrls, static fn ($value) => is_string($value) && $value !== ''));
            if ($mediaUrls) {
                $options['mediaUrl'] = $mediaUrls;
            }
        }

        if ($this->statusCallback) {
            $options['statusCallback'] = $this->statusCallback;
        }

        $message = $this->client->messages->create($this->formatDestination($to, 'whatsapp'), $options);

        return method_exists($message, 'toArray') ? $message->toArray() : ['sid' => $message->sid ?? null];
    }

    public function sendSms(string $to, string $body): array
    {
        $this->ensureClient();

        $options = [
            'from' => $this->formatFrom('sms'),
            'body' => $body,
        ];

        if ($this->statusCallback) {
            $options['statusCallback'] = $this->statusCallback;
        }

        $message = $this->client->messages->create($this->formatDestination($to, 'sms'), $options);

        return method_exists($message, 'toArray') ? $message->toArray() : ['sid' => $message->sid ?? null];
    }

    public function downloadMedia(string $url): array
    {
        $this->ensureClient();

        $response = $this->client->request('GET', $url);
        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;
        $content = method_exists($response, 'getContent') ? $response->getContent() : null;
        $headers = method_exists($response, 'getHeaders') ? $response->getHeaders() : [];

        if ($status !== null && $status >= 400) {
            throw new RuntimeException('Twilio retornó un error al descargar el adjunto. Código: ' . $status);
        }

        if ($content === null) {
            throw new RuntimeException('No fue posible descargar el adjunto de Twilio.');
        }

        $contentType = 'application/octet-stream';
        if (is_array($headers)) {
            foreach ($headers as $header => $value) {
                if (strtolower($header) === 'content-type') {
                    $contentType = is_array($value) ? (string) ($value[0] ?? $contentType) : (string) $value;
                    break;
                }
            }
        }

        return [
            'content' => $content,
            'content_type' => $contentType,
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

    private function ensureClient(): void
    {
        if (!$this->configured()) {
            throw new RuntimeException('No se han configurado las credenciales de Twilio.');
        }

        if (!class_exists(Client::class)) {
            throw new RuntimeException('El SDK oficial de Twilio no está disponible. Ejecuta "composer install" para completarlo.');
        }

        if (!$this->client) {
            $this->client = new Client($this->accountSid, $this->authToken);
        }
    }
}
