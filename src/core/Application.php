<?php

declare(strict_types=1);

namespace Core;

final class Application
{
    public function run(): void
    {
        echo '<h1>🚀 Framework ReservaLab</h1>';
        echo '<hr>';

        echo '<p><strong>Aplicación:</strong> ' . Config::get('APP_NAME') . '</p>';
        echo '<p><strong>Entorno:</strong> ' . Config::get('APP_ENV') . '</p>';
        echo '<p><strong>Zona Horaria:</strong> ' . Config::get('APP_TIMEZONE') . '</p>';
    }
}