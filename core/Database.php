<?php

namespace Core;

use RuntimeException;

class Database
{
    /** @var \PDO|null */
    private static $connection = null;

    /**
     * @return \PDO
     */
    public static function connection()
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../config/config.php';
            $db = $config['database'];

            if (!extension_loaded('pdo')) {
                throw new RuntimeException('La extensión PDO no está habilitada en PHP. Actívala para conectarte a la base de datos.');
            }

            if (!extension_loaded('pdo_mysql')) {
                throw new RuntimeException('Falta la extensión pdo_mysql requerida para conectar con MySQL/MariaDB. Actívala en tu PHP.');
            }

            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $db['driver'],
                $db['host'],
                $db['port'],
                $db['database'],
                $db['charset']
            );

            try {
                self::$connection = new \PDO($dsn, $db['username'], $db['password'], $db['options']);
            } catch (\PDOException $exception) {
                if ($config['app']['debug']) {
                    throw $exception;
                }
                throw new RuntimeException('Error al conectar con la base de datos.');
            }
        }

        return self::$connection;
    }
}
