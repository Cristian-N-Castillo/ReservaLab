<?php

declare(strict_types=1);

use App\Controllers\CursoController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

/** @var \Core\Router $router */

$router->get(
    '/cursos',
    [CursoController::class, 'index'],
    [AuthMiddleware::class, AdminMiddleware::class]
);

$router->get(
    '/cursos/create',
    [CursoController::class, 'create'],
    [AuthMiddleware::class, AdminMiddleware::class]
);

$router->post(
    '/cursos',
    [CursoController::class, 'store'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);