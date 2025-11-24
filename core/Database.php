<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    /** @var PDO|null */
    private static $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../config/config.php';
            $db = $config['database'];

            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $db['driver'],
                $db['host'],
                $db['port'],
                $db['database'],
                $db['charset']
            );

            try {
                self::$connection = new PDO($dsn, $db['username'], $db['password'], $db['options']);
            } catch (PDOException $exception) {
                if ($config['app']['debug']) {
                    throw $exception;
                }
                throw new PDOException('Error al conectar con la base de datos.');
            }
        }

        return self::$connection;
    }
}
