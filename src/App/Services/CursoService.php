<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Curso;
use App\Repositories\CursoRepository;
use InvalidArgumentException;

final class CursoService
{
    private CursoRepository $repository;

    public function __construct()
    {
        $this->repository = new CursoRepository();
    }

    /**
     * Obtiene todos los cursos.
     *
     * @return Curso[]
     */
    public function obtenerTodos(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Busca un curso por ID.
     */
    public function obtenerPorId(int $id): ?Curso
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea un nuevo curso.
     */
    public function crear(string $nombre): bool
    {
        $nombre = trim($nombre);

        if ($nombre === '') {
            throw new InvalidArgumentException(
                'El nombre del curso es obligatorio.'
            );
        }

        if (mb_strlen($nombre) > 50) {
            throw new InvalidArgumentException(
                'El nombre del curso no puede superar los 50 caracteres.'
            );
        }

        if ($this->repository->findByNombre($nombre)) {
            throw new InvalidArgumentException(
                'Ya existe un curso con ese nombre.'
            );
        }

        $curso = new Curso(
            nombre: $nombre,
            activo: true
        );

        return $this->repository->create($curso);
    }

    /**
     * Actualiza un curso.
     */
    public function actualizar(
        int $id,
        string $nombre,
        bool $activo
    ): bool {

        $curso = $this->repository->findById($id);

        if (!$curso) {
            throw new InvalidArgumentException(
                'El curso no existe.'
            );
        }

        $nombre = trim($nombre);

        if ($nombre === '') {
            throw new InvalidArgumentException(
                'El nombre del curso es obligatorio.'
            );
        }

        $curso->nombre = $nombre;
        $curso->activo = $activo;

        return $this->repository->update($curso);
    }

    /**
     * Eliminación lógica.
     */
    public function eliminar(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Total de cursos activos.
     */
    public function total(): int
    {
        return $this->repository->count();
    }
}