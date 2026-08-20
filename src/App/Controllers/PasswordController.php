<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UsuarioService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use InvalidArgumentException;
use RuntimeException;

final class PasswordController extends Controller
{
    private UsuarioService $service;

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    public function index(): string
    {
        return $this->view(
            'auth.cambiar_password',
            [
                'title' => 'Cambiar contraseña',
                'avatares' => UsuarioService::avatares(),
                'avatarSeleccionado' => (string) Session::get('avatar', ''),
            ],
            'auth'
        );
    }

    public function update(Request $request): void
    {
        $response = new Response();

        $idUsuario = (int) Session::get('usuario_id', 0);

        $nueva = (string) $request->input('password', '');
        $confirmacion = (string) $request->input('password_confirm', '');
        $avatar = (string) $request->input('avatar', '');

        try {

            $this->service->cambiarPassword($idUsuario, $nueva, $confirmacion);

            $this->service->actualizarAvatar($idUsuario, $avatar);

            Session::set('debe_cambiar_password', false);

            Session::set('avatar', $avatar);

            Session::flash(
                'success',
                'Su contraseña fue actualizada correctamente.'
            );

            $response->redirect('/dashboard');

        } catch (InvalidArgumentException|RuntimeException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );

            $response->redirect('/cambiar-password');
        }
    }
}
