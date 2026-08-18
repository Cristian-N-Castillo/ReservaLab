<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use RuntimeException;

final class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function index(): string
    {
        return $this->view(
            'auth.login',
            [
                'title' => 'Iniciar sesión'
            ],
            'auth'
        );
    }

    public function login(): void
    {
        $request = new Request();
        $response = new Response();

        $rut = trim((string) $request->input('rut'));
        $password = (string) $request->input('password');

        try {

            if (!$this->auth->login($rut, $password)) {

                Session::flash(
                    'error',
                    'RUT o contraseña incorrectos.'
                );

                $response->redirect('/');

                return;
            }

        } catch (RuntimeException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );

            $response->redirect('/');

            return;
        }

        $response->redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->auth->logout();

        (new Response())->redirect('/');
    }
}