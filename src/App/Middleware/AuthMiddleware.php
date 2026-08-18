<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Session;

final class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Única ruta accesible mientras debe_cambiar_password esté activo.
     */
    private const RUTA_CAMBIAR_PASSWORD = '/cambiar-password';

    public function handle(Request $request): void
    {
        if (!Session::has('usuario_id')) {

            (new Response())->redirect('/');
            return;
        }

        if (
            (bool) Session::get('debe_cambiar_password', false) &&
            $request->uri() !== self::RUTA_CAMBIAR_PASSWORD
        ) {

            (new Response())->redirect(self::RUTA_CAMBIAR_PASSWORD);
        }
    }
}