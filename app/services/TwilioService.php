<?php

namespace App\Services;

use RuntimeException;

class TwilioService
{
    private const CLIENT_CLASS = '\\Twilio\\Rest\\Client';

    private string $accountSid;
    private string $authToken;
    private ?string $whatsAppFrom;
    private ?string $smsFrom;
    private string $defaultCountryCode;
    private ?string $statusCallback;
    /** @var object|null */
    private $client = null;

    public function __construct(
        ?string $accountSid = null,
        ?string $authToken = null,
        ?string $whatsAppFrom = null,
        ?string $smsFrom = null,
        ?string $defaultCountryCode = null,
        ?string $statusCallback = null
    ) {
        $this->bootTwilioAutoload();

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

        if ($this->configured() && class_exists(self::CLIENT_CLASS)) {
            $clientClass = self::CLIENT_CLASS;
            $this->client = new $clientClass($this->accountSid, $this->authToken);
        }
    }

    public function configured(): bool
    {
        return $this->accountSid !== '' && $this->authToken !== '';
    }

    public function ready(): bool
    {
        return $this->configured() && (class_exists(self::CLIENT_CLASS) || $this->httpSupported());
    }

    public function sendWhatsApp(string $to, string $body, array $mediaUrls = []): array
    {
        $this->ensureClient();

        $mediaUrls = array_values(array_filter($mediaUrls, static fn ($value) => is_string($value) && $value !== ''));

        if ($this->client) {
            $options = [
                'from' => $this->formatFrom('whatsapp'),
                'body' => $body,
            ];

            if ($mediaUrls) {
                $options['mediaUrl'] = $mediaUrls;
            }

            if ($this->statusCallback) {
                $options['statusCallback'] = $this->statusCallback;
            }

            $message = $this->client->messages->create($this->formatDestination($to, 'whatsapp'), $options);

            return method_exists($message, 'toArray') ? $message->toArray() : ['sid' => $message->sid ?? null];
        }

        return $this->sendViaHttp('whatsapp', $to, $body, $mediaUrls);
    }

    public function sendSms(string $to, string $body): array
    {
        $this->ensureClient();

        if ($this->client) {
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

        return $this->sendViaHttp('sms', $to, $body);
    }

    public function downloadMedia(string $url): array
    {
        $this->ensureClient();

        if ($this->client) {
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

            $contentType = $this->resolveContentType($headers);

            return [
                'content' => $content,
                'content_type' => $contentType,
                'size' => strlen($content),
            ];
        }

        $response = $this->httpRequest('GET', $url);
        if ($response['status'] >= 400) {
            throw new RuntimeException('Twilio retornó un error al descargar el adjunto. Código: ' . $response['status']);
        }

        $content = $response['body'];
        if ($content === null) {
            throw new RuntimeException('No fue posible descargar el adjunto de Twilio.');
        }

        $contentType = $this->resolveContentType($response['headers']);

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

        if (class_exists(self::CLIENT_CLASS) && !$this->client) {
            $clientClass = self::CLIENT_CLASS;
            $this->client = new $clientClass($this->accountSid, $this->authToken);
        }

        if (!$this->client && !$this->httpSupported()) {
            throw new RuntimeException('El SDK oficial de Twilio no está disponible y el entorno no permite realizar peticiones HTTP directas.');
        }
    }

    private function bootTwilioAutoload(): void
    {
        if (class_exists(self::CLIENT_CLASS)) {
            return;
        }

        $root = dirname(__DIR__, 2);
        $candidates = [
            $root . '/vendor/autoload.php',
            $root . '/vendor/twilio/sdk/src/Twilio/autoload.php',
        ];

        foreach ($candidates as $file) {
            if (is_file($file)) {
                require_once $file;
                if (class_exists(self::CLIENT_CLASS)) {
                    break;
                }
            }
        }
    }

    private function httpSupported(): bool
    {
        if (function_exists('curl_init')) {
            return true;
        }

        $allowUrlFopen = ini_get('allow_url_fopen');
        if (!is_string($allowUrlFopen)) {
            return false;
        }

        $allowUrlFopen = strtolower(trim($allowUrlFopen));

        return in_array($allowUrlFopen, ['1', 'on', 'true'], true);
    }

    private function sendViaHttp(string $channel, string $to, string $body, array $mediaUrls = []): array
    {
        $endpoint = sprintf('https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json', rawurlencode($this->accountSid));

        $payload = [
            'To' => $this->formatDestination($to, $channel),
            'From' => $this->formatFrom($channel),
            'Body' => $body,
        ];

        if ($this->statusCallback) {
            $payload['StatusCallback'] = $this->statusCallback;
        }

        if ($mediaUrls) {
            $payload['MediaUrl'] = $mediaUrls;
        }

        $response = $this->httpRequest('POST', $endpoint, $payload);

        if ($response['status'] >= 400) {
            throw new RuntimeException('Twilio rechazó el envío del mensaje. Código: ' . $response['status']);
        }

        $bodyResponse = $response['body'];
        if ($bodyResponse === null || $bodyResponse === '') {
            return ['sid' => null];
        }

        $decoded = json_decode($bodyResponse, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return ['sid' => null];
    }

    private function httpRequest(string $method, string $url, array $data = []): array
    {
        $method = strtoupper($method);

        if ($method === 'GET' && $data) {
            $query = $this->buildQuery($data);
            if ($query !== '') {
                $separator = strpos($url, '?') !== false ? '&' : '?';
                $url .= $separator . $query;
            }
            $data = [];
        }

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            if ($ch === false) {
                throw new RuntimeException('No fue posible inicializar la conexión cURL hacia Twilio.');
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_USERPWD, $this->accountSid . ':' . $this->authToken);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: */*']);
            curl_setopt($ch, CURLOPT_HEADER, true);

            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildQuery($data));
            }

            $result = curl_exec($ch);
            if ($result === false) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new RuntimeException('No fue posible comunicarse con Twilio: ' . $error);
            }

            $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            curl_close($ch);

            $headersRaw = substr($result, 0, $headerSize) ?: '';
            $body = substr($result, $headerSize) ?: '';

            return [
                'status' => $status,
                'body' => $body,
                'headers' => $this->parseHeaders($headersRaw),
            ];
        }

        $headers = [
            'Authorization: Basic ' . base64_encode($this->accountSid . ':' . $this->authToken),
            'Accept: */*',
        ];

        $contextOptions = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true,
            ],
        ];

        if ($method === 'POST') {
            $contextOptions['http']['header'] .= "\r\nContent-Type: application/x-www-form-urlencoded";
            $contextOptions['http']['content'] = $this->buildQuery($data);
        }

        $context = stream_context_create($contextOptions);
        $body = @file_get_contents($url, false, $context);
        $status = 0;
        $responseHeaders = [];

        if (isset($http_response_header) && is_array($http_response_header)) {
            $responseHeaders = $this->parseHeaders(implode("\r\n", $http_response_header));
            foreach ($http_response_header as $headerLine) {
                if (preg_match('/\s(\d{3})\s/', $headerLine, $matches)) {
                    $status = (int) $matches[1];
                    break;
                }
            }
        }

        return [
            'status' => $status,
            'body' => $body === false ? null : $body,
            'headers' => $responseHeaders,
        ];
    }

    private function parseHeaders(string $rawHeaders): array
    {
        $headers = [];
        $lines = preg_split('/\r?\n/', trim($rawHeaders));
        if ($lines === false) {
            return $headers;
        }

        foreach ($lines as $line) {
            if ($line === '' || strpos($line, ':') === false) {
                continue;
            }

            [$name, $value] = explode(':', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!isset($headers[$name])) {
                $headers[$name] = [];
            }
            $headers[$name][] = $value;
        }

        return $headers;
    }

    private function resolveContentType(array $headers): string
    {
        foreach ($headers as $header => $value) {
            if (strtolower($header) === 'content-type') {
                return is_array($value) ? (string) ($value[0] ?? 'application/octet-stream') : (string) $value;
            }
        }

        return 'application/octet-stream';
    }

    private function buildQuery(array $data): string
    {
        $pairs = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $nested) {
                    if ($nested === null || $nested === '') {
                        continue;
                    }
                    $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $nested);
                }
                continue;
            }

            if ($value === null) {
                continue;
            }

            $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
        }

        return implode('&', $pairs);
    }
}
