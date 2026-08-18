<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Session;

/**
 * Restringe el acceso a usuarios con rol Administrador (id_rol = 1).
 *
 * Debe usarse siempre después de AuthMiddleware, ya que asume
 * que el usuario ya está autenticado.
 */
final class AdminMiddleware implements MiddlewareInterface
{
    private const ID_ROL_ADMINISTRADOR = 1;

    public function handle(Request $request): void
    {
        if ((int) Session::get('id_rol', 0) !== self::ID_ROL_ADMINISTRADOR) {

            Session::flash(
                'error',
                'No tiene permisos para acceder a esta sección.'
            );

            (new Response())->redirect('/dashboard');
        }
    }
}
