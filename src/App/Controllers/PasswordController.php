<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UsuarioService;
use Core\Controller;
use Core\Request;
use Core\Session;

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
        $idUsuario = (int) Session::get('usuario_id', 0);

        $nueva = (string) $request->input('password', '');
        $confirmacion = (string) $request->input('password_confirm', '');
        $avatar = (string) $request->input('avatar', '');

        $this->ejecutarConFlash(
            function () use ($idUsuario, $nueva, $confirmacion, $avatar): void {

                $this->service->cambiarPassword($idUsuario, $nueva, $confirmacion);

                $this->service->actualizarAvatar($idUsuario, $avatar);

                Session::set('debe_cambiar_password', false);

                Session::set('avatar', $avatar);
            },
            'Su contraseña fue actualizada correctamente.',
            '/dashboard',
            '/cambiar-password'
        );
    }
}
