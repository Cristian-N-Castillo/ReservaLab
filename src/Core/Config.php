<?php

declare(strict_types=1);

namespace Core;

use Dotenv\Dotenv;

final class Config
{
    private static array $config = [];

    public static function load(string $basePath): void
    {
        $dotenv = Dotenv::createImmutable($basePath);
        $dotenv->safeLoad();

        self::$config = $_ENV;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$config[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset(self::$config[$key]);
    }

    public static function all(): array
    {
        return self::$config;
    }
}