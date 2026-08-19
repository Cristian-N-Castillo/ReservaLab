<?php

declare(strict_types=1);

namespace Core;

abstract class Controller
{
    protected function view(
        string $view,
        array $data = [],
        ?string $layout = 'app'
    ): string {
        return View::render(
            $view,
            $data,
            $layout
        );
    }
}