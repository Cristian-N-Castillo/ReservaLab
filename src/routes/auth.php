<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\PasswordController;
use App\Controllers\PasswordResetController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;

/** @var \Core\Router $router */

$router->get(
    '/',
    [AuthController::class, 'index'],
    [GuestMiddleware::class]
);

$router->post(
    '/login',
    [AuthController::class, 'login'],
    [GuestMiddleware::class, CsrfMiddleware::class]
);

$router->get(
    '/logout',
    [AuthController::class, 'logout']
);

$router->get(
    '/cambiar-password',
    [PasswordController::class, 'index'],
    [AuthMiddleware::class]
);

$router->post(
    '/cambiar-password',
    [PasswordController::class, 'update'],
    [AuthMiddleware::class, CsrfMiddleware::class]
);

$router->get(
    '/recuperar-password',
    [PasswordResetController::class, 'index'],
    [GuestMiddleware::class]
);

$router->post(
    '/recuperar-password',
    [PasswordResetController::class, 'enviarCodigo'],
    [GuestMiddleware::class, CsrfMiddleware::class]
);

$router->get(
    '/recuperar-password/verificar',
    [PasswordResetController::class, 'verificar'],
    [GuestMiddleware::class]
);

$router->post(
    '/recuperar-password/verificar',
    [PasswordResetController::class, 'restablecer'],
    [GuestMiddleware::class, CsrfMiddleware::class]
);