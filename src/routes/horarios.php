<?php

declare(strict_types=1);

use App\Controllers\HorarioController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

/** @var \Core\Router $router */

$router->get(
    '/horarios',
    [HorarioController::class, 'index'],
    [AuthMiddleware::class, AdminMiddleware::class]
);