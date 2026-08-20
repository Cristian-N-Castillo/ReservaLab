<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Usuario;
use App\Services\UsuarioService;
use Core\Controller;
use Core\Request;
use Core\Session;
use RuntimeException;

final class PerfilController extends Controller
{
    private UsuarioService $service;

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    public function index(): string
    {
        $usuario = $this->service->obtener(
            (int) Session::get('usuario_id', 0)
        );

        if (!$usuario) {
            throw new RuntimeException('El usuario no existe.');
        }

        return $this->view(
            'usuarios.perfil',
            [
                'title' => 'Mi perfil',
                'usuario' => $usuario,
                'avatares' => UsuarioService::avatares(),
                'avatarSeleccionado' => $usuario->avatar,
            ]
        );
    }

    public function update(Request $request): void
    {
        $idUsuario = (int) Session::get('usuario_id', 0);

        $actual = $this->service->obtener($idUsuario);

        if (!$actual) {
            throw new RuntimeException('El usuario no existe.');
        }

        $usuario = new Usuario(
            id_usuario: $idUsuario,
            id_rol: $actual->id_rol,
            rut: $actual->rut,
            nombres: trim((string) $request->input('nombres', '')),
            apellidos: trim((string) $request->input('apellidos', '')),
            correo: trim((string) $request->input('correo', '')),
            telefono: trim((string) $request->input('telefono', '')),
            activo: $actual->activo
        );

        $avatar = (string) $request->input('avatar', '');

        $this->ejecutarConFlash(
            function () use ($usuario, $idUsuario, $avatar): void {

                $this->service->actualizar($usuario);

                $this->service->actualizarAvatar($idUsuario, $avatar);

                Session::set(
                    'nombre',
                    trim($usuario->nombres . ' ' . $usuario->apellidos)
                );

                Session::set('avatar', $avatar);
            },
            'Su perfil fue actualizado correctamente.',
            '/perfil'
        );
    }
}
