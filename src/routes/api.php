<?php

declare(strict_types=1);

use App\Controllers\ReservaApiController;

/** @var \Core\Router $router */

/*
 * Rutas públicas (sin AuthMiddleware): se acceden desde el correo
 * de confirmación de reserva. El token de la URL es la autorización.
 */

$router->get(
    '/api/reservas/{id}/confirmar/{token}',
    [ReservaApiController::class, 'confirmar']
);

$router->get(
    '/api/reservas/{id}/cancelar/{token}',
    [ReservaApiController::class, 'cancelar']
);
