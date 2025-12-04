<?php

namespace Core;

class Session
{
    /**
     * @return void
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @return void
     */
    public static function regenerate()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * @return void
     */
    public static function remove(string $key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @return void
     */
    public static function destroy()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }
}
