<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\CursoService;
use Core\Controller;
use Core\Request;
use Core\Response;
use InvalidArgumentException;

final class CursoController extends Controller
{
    private CursoService $service;

    public function __construct()
    {
        $this->service = new CursoService();
    }

    /**
     * Muestra el listado de cursos.
     */
    public function index(): string
    {
        return $this->view(
            'cursos.index',
            [
                'title'   => 'Cursos',
                'cursos'  => $this->service->obtenerTodos()
            ]
        );
    }

    /**
     * Muestra el formulario para crear un curso.
     */
    public function create(): string
    {
        return $this->view(
            'cursos.create',
            [
                'title' => 'Nuevo Curso'
            ]
        );
    }

    /**
     * Guarda un nuevo curso.
     */
    public function store(): void
    {
        $request = new Request();
        $response = new Response();

        $nombre = trim((string) $request->input('nombre'));

        try {

            $this->service->crear($nombre);

            // Más adelante reemplazaremos esto por un mensaje Flash.
            $response->redirect('/cursos');

        } catch (InvalidArgumentException $e) {

            // Más adelante aquí mostraremos el mensaje Flash.
            $response->redirect('/cursos/create');
        }
    }
}