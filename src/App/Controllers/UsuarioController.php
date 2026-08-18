<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Usuario;
use App\Services\RolService;
use App\Services\UsuarioService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use InvalidArgumentException;
use RuntimeException;

final class UsuarioController extends Controller
{
    private UsuarioService $service;
    private RolService $rolService;
    private Response $response;

    public function __construct()
    {
        $this->service = new UsuarioService();
        $this->rolService = new RolService();
        $this->response = new Response();
    }

    public function index(): string
    {
        return $this->view(
            'usuarios.index',
            [
                'title' => 'Usuarios',
                'usuarios' => $this->service->listar(),
            ]
        );
    }

    public function create(): string
    {
        return $this->view(
            'usuarios.create',
            [
                'title' => 'Nuevo Usuario',
                'roles' => $this->rolService->listar(),
            ]
        );
    }

    public function store(Request $request): void
    {
        $password = (string) $request->input('password', '');
        $passwordConfirm = (string) $request->input('password_confirm', '');

        if ($password !== $passwordConfirm) {

            Session::flash(
                'error',
                'Las contraseñas no coinciden.'
            );

            $this->response->redirect('/usuarios/crear');
            return;
        }

        $usuario = new Usuario(
            id_rol: (int) $request->input('id_rol', 0),
            rut: trim((string) $request->input('rut', '')),
            nombres: trim((string) $request->input('nombres', '')),
            apellidos: trim((string) $request->input('apellidos', '')),
            correo: trim((string) $request->input('correo', '')),
            telefono: trim((string) $request->input('telefono', '')),
            password: $password,
            activo: true
        );

        try {

            $this->service->crear($usuario);

            Session::flash(
                'success',
                'Usuario creado correctamente.'
            );

            $this->response->redirect('/usuarios');

        } catch (InvalidArgumentException|RuntimeException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );

            $this->response->redirect('/usuarios/crear');
        }
    }

    public function show(int $id): string
    {
        return $this->view(
            'usuarios.show',
            [
                'title' => 'Usuario',
                'usuario' => $this->service->obtener($id),
            ]
        );
    }

    public function edit(int $id): string
    {
        return $this->view(
            'usuarios.edit',
            [
                'title'   => 'Editar Usuario',
                'usuario' => $this->service->obtener($id),
                'roles'   => $this->rolService->listar(),
            ]
        );
    }

    public function update(int $id, Request $request): void
    {
        $usuario = new Usuario(
            id_usuario: $id,
            id_rol: (int) $request->input('id_rol', 0),
            rut: trim((string) $request->input('rut', '')),
            nombres: trim((string) $request->input('nombres', '')),
            apellidos: trim((string) $request->input('apellidos', '')),
            correo: trim((string) $request->input('correo', '')),
            telefono: trim((string) $request->input('telefono', '')),
            activo: true
        );

        try {

            $this->service->actualizar($usuario);

            Session::flash(
                'success',
                'Usuario actualizado correctamente.'
            );

            $this->response->redirect('/usuarios');

        } catch (InvalidArgumentException|RuntimeException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );

            $this->response->redirect("/usuarios/{$id}/editar");
        }
    }

    public function destroy(int $id): void
    {
        try {

            $this->service->eliminar($id);

            Session::flash(
                'success',
                'Usuario eliminado correctamente.'
            );

        } catch (InvalidArgumentException|RuntimeException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );
        }

        $this->response->redirect('/usuarios');
    }
}
