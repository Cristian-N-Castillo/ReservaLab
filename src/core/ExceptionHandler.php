<?php

declare(strict_types=1);

namespace Core;

use Throwable;

final class ExceptionHandler
{
    public function render(Throwable $exception): void
    {
        http_response_code(500);

        if (Config::get('APP_DEBUG') === 'true') {

            echo "<h1>Excepción capturada</h1>";

            echo "<hr>";

            echo "<strong>Mensaje:</strong><br>";
            echo $exception->getMessage();

            echo "<br><br>";

            echo "<strong>Archivo:</strong><br>";
            echo $exception->getFile();

            echo "<br><br>";

            echo "<strong>Línea:</strong><br>";
            echo $exception->getLine();

            echo "<br><br>";

            echo "<pre>";
            echo $exception->getTraceAsString();
            echo "</pre>";

            return;
        }

        echo "<h1>500 - Error interno del servidor</h1>";
    }
}