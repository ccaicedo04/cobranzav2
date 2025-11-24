<?php

namespace Core;

class Autoload
{
    public static function register()
    {
        $root = dirname(__DIR__);

        spl_autoload_register(function ($class) use ($root) {
            $normalized = ltrim($class, '\\');
            if ($normalized === '') {
                return;
            }

            $parts = explode('\\', $normalized);
            if (count($parts) === 0) {
                return;
            }

            $prefix = strtolower(array_shift($parts));
            if ($prefix !== 'app' && $prefix !== 'core') {
                return;
            }

            $segments = [$prefix];
            if (count($parts) > 0) {
                $segments[] = strtolower(array_shift($parts));
            }

            if (count($parts) > 0) {
                $segments[] = implode('/', $parts);
            }

            $file = $root . '/' . implode('/', $segments) . '.php';
            if (is_file($file)) {
                require_once $file;
            }
        });

        spl_autoload_register(function ($class) {
            $prefix = 'Twilio\\';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $baseDir = dirname(__DIR__) . '/app/libraries/twilio/src/Twilio/';
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
        });
    }
}
