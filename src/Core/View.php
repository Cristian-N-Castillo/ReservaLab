<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

final class View
{
    public static function render(
        string $view,
        array $data = [],
        ?string $layout = 'app'
    ): string {
        $viewPath = dirname(__DIR__) .
            '/App/Views/' .
            str_replace('.', '/', $view) .
            '.php';

        if (!file_exists($viewPath)) {
            throw new RuntimeException(
                "La vista '{$view}' no existe."
            );
        }

        extract($data, EXTR_SKIP);

        ob_start();

        require $viewPath;

        $content = ob_get_clean();

        if ($layout === null) {
            return $content;
        }

        $layoutPath = dirname(__DIR__) .
            '/App/Views/layouts/' .
            $layout .
            '.php';

        if (!file_exists($layoutPath)) {
            throw new RuntimeException(
                "El layout '{$layout}' no existe."
            );
        }

        ob_start();

        require $layoutPath;

        return ob_get_clean();
    }
}