<?php

namespace Core;

class Autoload
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $baseDir = dirname(__DIR__) . '/app/';
            $class = ltrim($class, '\\');
            $file = $baseDir . str_replace('\\', '/', $class) . '.php';
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
