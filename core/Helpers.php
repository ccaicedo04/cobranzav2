<?php

namespace Core;

class Helpers
{
    public static function baseUrl(string $path = ''): string
    {
        $config = require __DIR__ . '/../config/config.php';
        $base = rtrim($config['app']['base_url'], '/');

        return $base . '/' . ltrim($path, '/');
    }

    public static function redirect(string $path): void
    {
        header('Location: ' . self::baseUrl($path));
        exit;
    }

    public static function csrfToken(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set('_csrf_token', $token);

        return $token;
    }

    public static function validateCsrf(string $token): bool
    {
        Session::start();
        $stored = Session::get('_csrf_token');

        return hash_equals((string) $stored, (string) $token);
    }
}
