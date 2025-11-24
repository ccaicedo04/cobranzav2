<?php

declare(strict_types=1);

namespace Core;

use ZipArchive;

class DompdfSetup
{
    private const VERSION = '3.1.4';

    /**
     * Garantiza que el autoload oficial de DOMPDF esté disponible.
     * - Si existe dompdf-3.1.4.zip en app/libraries/dompdf/ se descomprime automáticamente.
     * - Si ya está instalado, sólo prepara los directorios de cache y fonts.
     *
     * @return bool true cuando el autoloader quedó listo.
     */
    public static function bootstrap(): bool
    {
        $base = dirname(__DIR__) . '/app/libraries/dompdf';
        $autoload = $base . '/autoload.inc.php';

        if (!is_file($autoload)) {
            self::extractFromZip($base);
        }

        if (is_file($autoload)) {
            require_once $autoload;
            self::ensureRuntimeDirs($base);
            return true;
        }

        return false;
    }

    private static function extractFromZip(string $base): void
    {
        $zipPath = $base . '/dompdf-' . self::VERSION . '.zip';
        if (!is_file($zipPath) || !class_exists(ZipArchive::class)) {
            return;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return;
        }

        $zip->extractTo($base);
        $zip->close();

        $versionDir = $base . '/dompdf-' . self::VERSION;
        if (is_dir($versionDir)) {
            self::moveContents($versionDir, $base);
            @rmdir($versionDir);
        }
    }

    private static function moveContents(string $from, string $to): void
    {
        $items = scandir($from) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $src = $from . '/' . $item;
            $dest = $to . '/' . $item;

            if (is_dir($src)) {
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
                self::moveContents($src, $dest);
                @rmdir($src);
            } else {
                @rename($src, $dest);
            }
        }
    }

    private static function ensureRuntimeDirs(string $base): void
    {
        foreach (['lib/fonts', 'lib/cache'] as $relative) {
            $dir = $base . '/' . $relative;
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
}
