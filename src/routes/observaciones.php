<?php

declare(strict_types=1);

use App\Controllers\ObservacionController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

/** @var \Core\Router $router */

$router->get(
    '/observaciones',
    [ObservacionController::class, 'index'],
    [AuthMiddleware::class, AdminMiddleware::class]
);

// La descarga del PDF y el detalle por reserva quedan abiertos a
// cualquier usuario autenticado; el propio controlador valida que
// solo pueda verlos el Administrador o el docente dueño de la reserva.
$router->get(
    '/observaciones/{id}/pdf',
    [ObservacionController::class, 'descargarPdf'],
    [AuthMiddleware::class]
);

$router->get(
    '/reservas/{id}/observaciones',
    [ObservacionController::class, 'porReserva'],
    [AuthMiddleware::class]
);

$router->post(
    '/reservas/{id}/observaciones',
    [ObservacionController::class, 'store'],
    [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]
);
