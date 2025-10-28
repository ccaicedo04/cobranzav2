<?php

namespace Core;

use RuntimeException;

class Mailer
{
    public static function send(array $settings, array $message): void
    {
        $host = trim((string) ($settings['host'] ?? ''));
        $port = (int) ($settings['port'] ?? 587);
        $username = trim((string) ($settings['username'] ?? ''));
        $password = (string) ($settings['password'] ?? '');
        $fromEmail = trim((string) ($settings['from_email'] ?? $username));
        $fromName = trim((string) ($settings['from_name'] ?? 'Plataforma de Cobranzas'));

        $toEmail = trim((string) ($message['to'] ?? ''));
        $subject = (string) ($message['subject'] ?? '');
        $htmlBody = (string) ($message['html'] ?? '');
        $textBody = (string) ($message['text'] ?? strip_tags($htmlBody));

        if ($host === '' || $username === '' || $password === '' || $fromEmail === '' || $toEmail === '') {
            throw new RuntimeException('Configuración SMTP incompleta.');
        }
        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Direcciones de correo inválidas.');
        }

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ]);

        $candidates = array_values(array_unique(array_filter([
            $host,
            preg_match('/\.co$/i', $host) ? preg_replace('/\.co$/i', '.com', $host) : null,
            'smtp.gmail.com',
        ], static fn ($value) => is_string($value) && $value !== '')));

        $socket = null;
        $connectionHost = $host;
        $lastError = '';
        $lastCode = 0;

        foreach ($candidates as $candidate) {
            $resource = @stream_socket_client(
                sprintf('tcp://%s:%d', $candidate, $port),
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($resource) {
                $socket = $resource;
                $connectionHost = $candidate;
                break;
            }

            $lastError = $errstr;
            $lastCode = $errno;
        }

        if (!$socket) {
            throw new RuntimeException('No fue posible conectar con el servidor SMTP: ' . $lastError, $lastCode);
        }

        stream_set_timeout($socket, 30);
        self::expect($socket, [220]);

        self::command($socket, 'EHLO ' . self::hostGreeting($connectionHost));
        self::command($socket, 'STARTTLS', [220]);

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            throw new RuntimeException('No fue posible establecer un canal seguro (STARTTLS).');
        }

        self::command($socket, 'EHLO ' . self::hostGreeting($connectionHost));
        self::command($socket, 'AUTH LOGIN', [334]);
        self::command($socket, base64_encode($username), [334]);
        self::command($socket, base64_encode($password), [235]);

        self::command($socket, 'MAIL FROM:<' . $fromEmail . '>');
        self::command($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251]);
        self::command($socket, 'DATA', [354]);

        $boundary = '=_Mailer_' . bin2hex(random_bytes(8));
        $headers = [
            'From: ' . self::formatAddress($fromEmail, $fromName),
            'To: ' . self::formatAddress($toEmail, $message['to_name'] ?? ''),
            'Subject: ' . self::encodeHeader($subject),
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        $bodyParts = [];
        $bodyParts[] = '--' . $boundary;
        $bodyParts[] = 'Content-Type: text/plain; charset=UTF-8';
        $bodyParts[] = 'Content-Transfer-Encoding: 8bit';
        $bodyParts[] = '';
        $bodyParts[] = $textBody;
        $bodyParts[] = '';
        $bodyParts[] = '--' . $boundary;
        $bodyParts[] = 'Content-Type: text/html; charset=UTF-8';
        $bodyParts[] = 'Content-Transfer-Encoding: 8bit';
        $bodyParts[] = '';
        $bodyParts[] = $htmlBody;
        $bodyParts[] = '';
        $bodyParts[] = '--' . $boundary . '--';
        $bodyParts[] = '';

        $data = implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $bodyParts);
        self::raw($socket, $data . "\r\n.\r\n");
        self::expect($socket, [250]);

        self::command($socket, 'QUIT', [221]);
        fclose($socket);
    }

    private static function command($socket, string $command, array $expected = [250]): array
    {
        self::raw($socket, $command . "\r\n");
        return self::expect($socket, $expected);
    }

    private static function expect($socket, array $expected): array
    {
        $response = [];
        $code = 0;
        while (($line = fgets($socket, 512)) !== false) {
            $response[] = rtrim($line, "\r\n");
            if (preg_match('/^(\d{3})(?:\s|-)/', $line, $matches)) {
                $code = (int) $matches[1];
                if ($line[3] === ' ') {
                    break;
                }
            }
        }

        if (!in_array($code, $expected, true)) {
            throw new RuntimeException('Error SMTP (' . $code . '): ' . implode(' | ', $response));
        }

        return $response;
    }

    private static function raw($socket, string $payload): void
    {
        $bytes = fwrite($socket, $payload);
        if ($bytes === false) {
            throw new RuntimeException('Fallo al enviar datos al servidor SMTP.');
        }
    }

    private static function formatAddress(string $email, string $name = ''): string
    {
        if ($name === '') {
            return $email;
        }

        return self::encodeHeader($name) . ' <' . $email . '>';
    }

    private static function encodeHeader(string $value): string
    {
        if (!preg_match('/[\x80-\xFF]/', $value)) {
            return $value;
        }

        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private static function hostGreeting(string $host): string
    {
        if ($host === '') {
            return 'localhost';
        }

        $sanitized = preg_replace('/[^A-Za-z0-9.-]/', '', $host) ?: 'localhost';

        return strtolower($sanitized);
    }
}
