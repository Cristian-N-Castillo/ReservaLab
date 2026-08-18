<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\LaboratorioService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use InvalidArgumentException;

final class LaboratorioController extends Controller
{
    private LaboratorioService $service;
    private Response $response;

    public function __construct()
    {
        $this->service = new LaboratorioService();
        $this->response = new Response();
    }

    /**
     * Muestra el listado de laboratorios.
     *
     * El Administrador ve todos (activos e inactivos) con acciones
     * de gestión. El Docente solo ve el listado de laboratorios activos,
     * sin acciones de administración.
     */
    public function index(): string
    {
        $esAdmin = (int) Session::get('id_rol', 0) === 1;

        return $this->view(
            'laboratorios.index',
            [
                'title' => 'Laboratorios',
                'laboratorios' => $esAdmin
                    ? $this->service->listarTodos()
                    : $this->service->listar(),
                'esAdmin' => $esAdmin,
            ]
        );
    }

    /**
     * Muestra el formulario para crear un laboratorio.
     */
    public function create(): string
    {
        return $this->view(
            'laboratorios.create',
            [
                'title' => 'Nuevo Laboratorio',
            ]
        );
    }

    /**
     * Guarda un nuevo laboratorio.
     */
    public function store(Request $request): void
    {
        try {

            $this->service->crear(
                nombre: (string) $request->input('nombre', ''),
                ubicacion: (string) $request->input('ubicacion', ''),
                descripcion: (string) $request->input('descripcion', ''),
                capacidad: (int) $request->input('capacidad', 0),
                especialidadPrioritaria: $request->input('especialidad_prioritaria') !== null
                    ? (string) $request->input('especialidad_prioritaria')
                    : null
            );

            Session::flash(
                'success',
                'Laboratorio creado correctamente.'
            );

            $this->response->redirect('/laboratorios');

        } catch (InvalidArgumentException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );

            $this->response->redirect('/laboratorios/crear');
        }
    }

    /**
     * Muestra el formulario para editar un laboratorio.
     */
    public function edit(int $id): string
    {
        $laboratorio = $this->service->buscarPorId($id);

        if (!$laboratorio) {
            Session::flash(
                'error',
                'El laboratorio no existe.'
            );

            $this->response->redirect('/laboratorios');
        }

        return $this->view(
            'laboratorios.edit',
            [
                'title' => 'Editar Laboratorio',
                'laboratorio' => $laboratorio,
            ]
        );
    }

    /**
     * Actualiza un laboratorio existente.
     */
    public function update(int $id, Request $request): void
    {
        try {

            $this->service->actualizar(
                id: $id,
                nombre: (string) $request->input('nombre', ''),
                ubicacion: (string) $request->input('ubicacion', ''),
                descripcion: (string) $request->input('descripcion', ''),
                capacidad: (int) $request->input('capacidad', 0),
                especialidadPrioritaria: $request->input('especialidad_prioritaria') !== null
                    ? (string) $request->input('especialidad_prioritaria')
                    : null,
                activo: $request->input('activo') !== null
            );

            Session::flash(
                'success',
                'Laboratorio actualizado correctamente.'
            );

            $this->response->redirect('/laboratorios');

        } catch (InvalidArgumentException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );

            $this->response->redirect("/laboratorios/{$id}/editar");
        }
    }

    /**
     * Desactiva un laboratorio.
     */
    public function destroy(int $id): void
    {
        $this->cambiarEstado($id, false, 'desactivado');
    }

    /**
     * Activa un laboratorio.
     */
    public function activar(int $id): void
    {
        $this->cambiarEstado($id, true, 'activado');
    }

    private function cambiarEstado(int $id, bool $activo, string $accion): void
    {
        try {

            $this->service->cambiarEstado($id, $activo);

            Session::flash(
                'success',
                "Laboratorio {$accion} correctamente."
            );

        } catch (InvalidArgumentException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );
        }

        $this->response->redirect('/laboratorios');
    }
}
