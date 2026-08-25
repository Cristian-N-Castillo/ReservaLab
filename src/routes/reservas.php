<?php

declare(strict_types=1);

use App\Controllers\ReservaController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

/** @var \Core\Router $router */

$router->get(
    '/reservas',
    [ReservaController::class, 'index'],
    [AuthMiddleware::class]
);

$router->get(
    '/reservas/calendario',
    [ReservaController::class, 'calendario'],
    [AuthMiddleware::class]
);

$router->post(
    '/reservas',
    [ReservaController::class, 'store'],
    [AuthMiddleware::class, CsrfMiddleware::class]
);

$router->post(
    '/reservas/{id}/confirmar',
    [ReservaController::class, 'confirmar'],
    [AuthMiddleware::class, CsrfMiddleware::class]
);

$router->post(
    '/reservas/{id}/cancelar',
    [ReservaController::class, 'cancelar'],
    [AuthMiddleware::class, CsrfMiddleware::class]
);