<?php

namespace Core;

class Helpers
{
    public static function baseUrl(string $path = ''): string
    {
        $config = require __DIR__ . '/../config/config.php';
        $base = trim((string) ($config['app']['base_url'] ?? ''));

        if ($base === '') {
            $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
            if ($scriptName !== '') {
                $directory = str_replace('\\', '/', rtrim(dirname($scriptName), '/'));
                if ($directory !== '') {
                    $base = $directory;
                }
            }
        }

        $base = rtrim($base, '/');
        $path = ltrim($path, '/');

        if ($base === '') {
            return $path === '' ? '/' : '/' . $path;
        }

        return $path === '' ? $base : $base . '/' . $path;
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

    public static function tenantContext(): array
    {
        $user = Session::get('user', []);
        $context = Session::get('context', []);

        $idColegio = $context['id_colegio'] ?? null;
        if (!$idColegio && !empty($user['colegios_permitidos'])) {
            $idColegio = $user['colegios_permitidos'][0];
        }
        if (!$idColegio && !empty($user['id_colegio'])) {
            $idColegio = $user['id_colegio'];
        }

        $idSede = $context['id_sede'] ?? null;
        if (!$idSede && !empty($user['sedes_permitidas'])) {
            $idSede = $user['sedes_permitidas'][0];
        }
        if (!$idSede && !empty($user['id_sede'])) {
            $idSede = $user['id_sede'];
        }

        return [
            'id_colegio' => $idColegio ? (int) $idColegio : null,
            'id_sede' => $idSede ? (int) $idSede : null,
        ];
    }
}
