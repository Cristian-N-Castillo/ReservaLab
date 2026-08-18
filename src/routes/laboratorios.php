<?php

declare(strict_types=1);

use App\Controllers\LaboratorioController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

/** @var \Core\Router $router */

// Visible para cualquier usuario autenticado (Docente y Administrador).
$router->get(
    '/laboratorios',
    [LaboratorioController::class, 'index'],
    [AuthMiddleware::class]
);

// Gestión (crear/editar/activar/desactivar): solo Administrador.
$router->get(
    '/laboratorios/crear',
    [LaboratorioController::class, 'create'],
    [AuthMiddleware::class, AdminMiddleware::class]
);

$router->post(
    '/laboratorios',
    [LaboratorioController::class, 'store'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);

$router->get(
    '/laboratorios/{id}/editar',
    [LaboratorioController::class, 'edit'],
    [AuthMiddleware::class, AdminMiddleware::class]
);

$router->post(
    '/laboratorios/{id}',
    [LaboratorioController::class, 'update'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);

$router->post(
    '/laboratorios/{id}/desactivar',
    [LaboratorioController::class, 'destroy'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);

$router->post(
    '/laboratorios/{id}/activar',
    [LaboratorioController::class, 'activar'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);
