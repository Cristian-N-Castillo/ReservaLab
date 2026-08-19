<?php

declare(strict_types=1);

namespace Core;

final class Response
{
    /**
     * Establece el código de respuesta HTTP.
     */
    public function status(int $code): self
    {
        http_response_code($code);

        return $this;
    }

    /**
     * Envía una respuesta HTML.
     */
    public function html(string $content): void
    {
        header('Content-Type: text/html; charset=UTF-8');

        echo $content;
    }

    /**
     * Envía una respuesta JSON.
     */
    public function json(array $data, int $status = 200): void
    {
        $this->status($status);

        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Redirecciona a otra URL.
     */
    public function redirect(string $url): never
    {
        header("Location: {$url}");
        exit;
    }
}