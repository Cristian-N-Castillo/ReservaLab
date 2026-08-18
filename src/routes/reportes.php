<?php

declare(strict_types=1);

use App\Controllers\ReporteController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

/** @var \Core\Router $router */

$router->get(
    '/reportes',
    [ReporteController::class, 'index'],
    [AuthMiddleware::class, AdminMiddleware::class]
);
