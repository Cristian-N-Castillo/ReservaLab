<?php
declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s',
            Config::get('DB_CONNECTION'),
            Config::get('DB_HOST'),
            Config::get('DB_PORT'),
            Config::get('DB_DATABASE')
        );

        try {
            self::$connection = new PDO(
                $dsn,
                Config::get('DB_USERNAME'),
                Config::get('DB_PASSWORD'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return self::$connection;
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'No fue posible conectar con la base de datos.',
                previous: $exception
            );
        }
    }
}