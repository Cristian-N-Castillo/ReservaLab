<?php

declare(strict_types=1);

use App\Controllers\DashboardController;
use App\Controllers\PerfilController;
use App\Controllers\TutorialController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

/** @var \Core\Router $router */

$router->get(
    '/dashboard',
    [DashboardController::class, 'index'],
    [AuthMiddleware::class]
);

$router->post(
    '/tutorial/visto',
    [TutorialController::class, 'marcarVisto'],
    [AuthMiddleware::class, CsrfMiddleware::class]
);

$router->get(
    '/perfil',
    [PerfilController::class, 'index'],
    [AuthMiddleware::class]
);

$router->post(
    '/perfil',
    [PerfilController::class, 'update'],
    [AuthMiddleware::class, CsrfMiddleware::class]
);