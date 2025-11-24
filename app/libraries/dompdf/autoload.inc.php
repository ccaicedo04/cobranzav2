<?php
/**
 * Autoload minimal DOMPDF-compatible stubs for Composer-free setups.
 *
 * Place the official standalone package contents under app/libraries/dompdf/
 * to replace these lightweight implementations. This file mirrors the
 * upstream entrypoint path so public/index.php can require it directly.
 */

spl_autoload_register(function ($class) {
    $prefix = 'Dompdf\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative = substr($class, $len);
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
