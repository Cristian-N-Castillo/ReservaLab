<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Csrf;
use Core\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Session;

/**
 * Verifica el token CSRF en peticiones POST.
 *
 * Debe agregarse a toda ruta POST que reciba datos de un
 * formulario propio del sistema.
 */
final class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request): void
    {
        if (!$request->isPost()) {
            return;
        }

        $token = $request->input('_token');

        if (!Csrf::verify(is_string($token) ? $token : null)) {

            Session::flash(
                'error',
                'Su sesión expiró o la solicitud no es válida. Intente nuevamente.'
            );

            $referer = $_SERVER['HTTP_REFERER'] ?? '/';

            (new Response())->redirect($referer);
        }
    }
}
