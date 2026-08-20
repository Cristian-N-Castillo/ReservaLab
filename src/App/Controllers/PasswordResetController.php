<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UsuarioService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;

final class PasswordResetController extends Controller
{
    private UsuarioService $service;
    private Response $response;

    public function __construct()
    {
        $this->service = new UsuarioService();
        $this->response = new Response();
    }

    public function index(): string
    {
        return $this->view(
            'auth.recuperar_password',
            [
                'title' => 'Recuperar contraseña',
                'identificador' => (string) Session::get('reset_identificador', ''),
            ],
            'auth'
        );
    }

    public function enviarCodigo(Request $request): void
    {
        $identificador = trim((string) $request->input('identificador', ''));

        if ($identificador === '') {

            Session::flash('error', 'Debe ingresar su RUT o correo.');

            $this->response->redirect('/recuperar-password');
        }

        $this->service->solicitarRecuperacion($identificador);

        Session::set('reset_identificador', $identificador);

        Session::flash(
            'success',
            'Si los datos ingresados son correctos, te enviamos un código de verificación a tu correo.'
        );

        $this->response->redirect('/recuperar-password/verificar');
    }

    public function verificar(): string
    {
        if (!Session::has('reset_identificador')) {
            $this->response->redirect('/recuperar-password');
        }

        return $this->view(
            'auth.verificar_codigo',
            [
                'title' => 'Verificar código',
            ],
            'auth'
        );
    }

    public function restablecer(Request $request): void
    {
        $identificador = (string) Session::get('reset_identificador', '');
        $codigo = trim((string) $request->input('codigo', ''));
        $nueva = (string) $request->input('password', '');
        $confirmacion = (string) $request->input('password_confirm', '');

        if ($identificador === '') {
            $this->response->redirect('/recuperar-password');
        }

        $this->ejecutarConFlash(
            function () use ($identificador, $codigo, $nueva, $confirmacion): void {

                $this->service->restablecerPasswordConCodigo(
                    $identificador,
                    $codigo,
                    $nueva,
                    $confirmacion
                );

                Session::remove('reset_identificador');
            },
            'Tu contraseña fue restablecida correctamente. Ya puedes iniciar sesión.',
            '/',
            '/recuperar-password/verificar'
        );
    }
}
