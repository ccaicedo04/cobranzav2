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

        self::normalizeInstall($base);

        if (!is_file($autoload)) {
            self::extractFromZip($base);
            self::normalizeInstall($base);
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
        $zipPaths = [
            $base . '/dompdf-' . self::VERSION . '.zip',
            $base . '/dompdf/dompdf-' . self::VERSION . '.zip',
            $base . '/dompdf.zip',
            $base . '/dompdf/dompdf.zip',
        ];

        $found = null;
        foreach ($zipPaths as $candidate) {
            if (is_file($candidate)) {
                $found = $candidate;
                break;
            }
        }

        if ($found === null || !class_exists(ZipArchive::class)) {
            return;
        }

        $zip = new ZipArchive();
        if ($zip->open($found) !== true) {
            return;
        }

        $zip->extractTo(dirname($found));
        $zip->close();
    }

    private static function normalizeInstall(string $base): void
    {
        $candidates = [
            $base . '/autoload.inc.php' => $base,
            $base . '/dompdf/autoload.inc.php' => $base . '/dompdf',
            $base . '/dompdf-' . self::VERSION . '/autoload.inc.php' => $base . '/dompdf-' . self::VERSION,
            $base . '/dompdf/dompdf-' . self::VERSION . '/autoload.inc.php' => $base . '/dompdf/dompdf-' . self::VERSION,
        ];

        foreach ($candidates as $autoload => $source) {
            if (!is_file($autoload)) {
                continue;
            }

            $sourceDir = realpath($source);
            $baseDir = realpath($base);

            if ($sourceDir !== false && $baseDir !== false && $sourceDir !== $baseDir) {
                self::flattenInstall($source, $base);
            }

            break;
        }
    }

    private static function flattenInstall(string $from, string $to): void
    {
        if (!is_dir($from)) {
            return;
        }

        if (realpath($from) === realpath($to)) {
            return;
        }

        self::moveContents($from, $to);
        @rmdir($from);
    }

    private static function moveContents(string $from, string $to): void
    {
        if (realpath($from) === realpath($to)) {
            return;
        }

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
                if (is_file($dest)) {
                    @unlink($dest);
                }
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
