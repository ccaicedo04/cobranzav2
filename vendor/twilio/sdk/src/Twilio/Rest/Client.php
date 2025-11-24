<?php

namespace Twilio\Rest;

use RuntimeException;

class Client
{
    /** @var string */
    private $accountSid;

    /** @var string */
    private $authToken;

    /** @var Messages */
    public $messages;

    public function __construct($accountSid, $authToken)
    {
        $this->accountSid = (string) $accountSid;
        $this->authToken = (string) $authToken;
        $this->messages = new Messages($this);
    }

    public function getAccountSid(): string
    {
        return $this->accountSid;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function request($method, $url, array $params = [], array $headers = []): Response
    {
        $http = HttpClient::request($method, $url, $params, $headers, $this->accountSid, $this->authToken);
        return new Response($http['status'], $http['body'], $http['headers']);
    }
}

class Messages
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create($to, array $options = [])
    {
        $payload = [
            'To' => (string) $to,
            'From' => (string) ($options['from'] ?? ''),
            'Body' => (string) ($options['body'] ?? ''),
        ];

        if (!empty($options['mediaUrl'])) {
            $payload['MediaUrl'] = (array) $options['mediaUrl'];
        }

        if (!empty($options['statusCallback'])) {
            $payload['StatusCallback'] = (string) $options['statusCallback'];
        }

        $endpoint = sprintf(
            'https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json',
            rawurlencode($this->client->getAccountSid())
        );

        $response = HttpClient::request('POST', $endpoint, $payload, [], $this->client->getAccountSid(), $this->client->getAuthToken());

        if ($response['status'] >= 400) {
            throw new RuntimeException('Twilio rechazó el envío del mensaje. Código: ' . $response['status']);
        }

        $data = [];
        if (!empty($response['body'])) {
            $decoded = json_decode($response['body'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data = $decoded;
            }
        }

        return new Message($data);
    }
}

class Message
{
    /** @var array */
    private $payload;

    /** @var string|null */
    public $sid;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->sid = isset($payload['sid']) ? (string) $payload['sid'] : null;
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}

class Response
{
    /** @var int */
    private $status;

    /** @var string|null */
    private $content;

    /** @var array */
    private $headers;

    public function __construct($status, $content, array $headers)
    {
        $this->status = (int) $status;
        $this->content = $content !== null ? (string) $content : null;
        $this->headers = $headers;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}

class HttpClient
{
    public static function request($method, $url, array $data, array $headers, $sid, $token): array
    {
        $method = strtoupper((string) $method);
        $headers = array_merge(['Accept: */*'], $headers);

        if ($method === 'GET' && $data) {
            $query = self::buildQuery($data);
            if ($query !== '') {
                $url .= (strpos($url, '?') === false ? '?' : '&') . $query;
            }
            $data = [];
        }

        if (function_exists('curl_init')) {
            return self::requestWithCurl($method, $url, $data, $headers, $sid, $token);
        }

        if (self::allowUrlFopen()) {
            return self::requestWithStream($method, $url, $data, $headers, $sid, $token);
        }

        throw new RuntimeException('No hay transporte HTTP disponible para comunicarse con Twilio.');
    }

    private static function requestWithCurl($method, $url, array $data, array $headers, $sid, $token): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('No fue posible inicializar cURL.');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_USERPWD, $sid . ':' . $token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, self::buildQuery($data));
        }

        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Error al comunicarse con Twilio mediante cURL: ' . $error);
        }

        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $headersRaw = substr($result, 0, $headerSize) ?: '';
        $body = substr($result, $headerSize) ?: '';

        return [
            'status' => $status,
            'body' => $body,
            'headers' => self::parseHeaders($headersRaw),
        ];
    }

    private static function requestWithStream($method, $url, array $data, array $headers, $sid, $token): array
    {
        $headers[] = 'Authorization: Basic ' . base64_encode($sid . ':' . $token);
        $options = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true,
            ],
        ];

        if ($method === 'POST') {
            $options['http']['header'] .= "\r\nContent-Type: application/x-www-form-urlencoded";
            $options['http']['content'] = self::buildQuery($data);
        }

        $context = stream_context_create($options);
        $body = @file_get_contents($url, false, $context);

        $status = 0;
        $responseHeaders = [];
        if (isset($http_response_header) && is_array($http_response_header)) {
            $responseHeaders = self::parseHeaders(implode("\r\n", $http_response_header));
            foreach ($http_response_header as $line) {
                if (preg_match('/\s(\d{3})\s/', $line, $matches)) {
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

    private static function buildQuery(array $data): string
    {
        $parts = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $parts[] = rawurlencode($key) . '=' . rawurlencode((string) $item);
                }
            } else {
                $parts[] = rawurlencode($key) . '=' . rawurlencode((string) $value);
            }
        }

        return implode('&', $parts);
    }

    private static function parseHeaders($raw): array
    {
        $headers = [];
        $lines = preg_split('/\r?\n/', (string) $raw) ?: [];
        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }
            [$name, $value] = array_map('trim', explode(':', $line, 2));
            $lower = strtolower($name);
            if (!isset($headers[$lower])) {
                $headers[$lower] = $value;
                continue;
            }
            if (!is_array($headers[$lower])) {
                $headers[$lower] = [$headers[$lower]];
            }
            $headers[$lower][] = $value;
        }

        return $headers;
    }

    private static function allowUrlFopen(): bool
    {
        $value = ini_get('allow_url_fopen');
        if (!is_string($value)) {
            return false;
        }
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'on', 'true'], true);
    }
}
