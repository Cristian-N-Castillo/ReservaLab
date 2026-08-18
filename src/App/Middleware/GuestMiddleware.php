<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Session;

final class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request): void
    {
        if (Session::has('usuario_id')) {

            (new Response())->redirect('/dashboard');

        }
    }
}