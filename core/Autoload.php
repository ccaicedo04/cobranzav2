<?php

namespace Core;

class Autoload
{
    public static function register()
    {
        $baseDirs = [
            dirname(__DIR__) . '/app/',
            dirname(__DIR__) . '/core/',
        ];

        spl_autoload_register(function ($class) use ($baseDirs) {
            $class = ltrim($class, '\\');
            $relative = str_replace('\\', '/', $class) . '.php';

            foreach ($baseDirs as $baseDir) {
                $file = $baseDir . $relative;
                if (is_file($file)) {
                    require_once $file;
                    return;
                }
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
