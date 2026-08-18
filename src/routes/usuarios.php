<?php

declare(strict_types=1);

use App\Controllers\UsuarioController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

/** @var \Core\Router $router */

$router->get(
    '/usuarios',
    [UsuarioController::class, 'index'],
    [AuthMiddleware::class, AdminMiddleware::class]
);

$router->get(
    '/usuarios/crear',
    [UsuarioController::class, 'create'],
    [AuthMiddleware::class, AdminMiddleware::class]
);

$router->post(
    '/usuarios',
    [UsuarioController::class, 'store'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);

$router->get(
    '/usuarios/{id}',
    [UsuarioController::class, 'show'],
    [AuthMiddleware::class, AdminMiddleware::class]
);

$router->get(
    '/usuarios/{id}/editar',
    [UsuarioController::class, 'edit'],
    [AuthMiddleware::class, AdminMiddleware::class]
);
$router->post(
    '/usuarios/{id}',
    [UsuarioController::class, 'update'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);
$router->post(
    '/usuarios/{id}/eliminar',
    [UsuarioController::class, 'destroy'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);