<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Laboratorio;
use App\Repositories\LaboratorioRepository;
use InvalidArgumentException;

final class LaboratorioService
{
    private LaboratorioRepository $repository;

    public function __construct()
    {
        $this->repository = new LaboratorioRepository();
    }

    /**
     * Obtiene todos los laboratorios activos.
     *
     * @return Laboratorio[]
     */
    public function listar(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Obtiene todos los laboratorios, activos e inactivos.
     *
     * Utilizado en la pantalla de administración.
     *
     * @return Laboratorio[]
     */
    public function listarTodos(): array
    {
        return $this->repository->findAllAdmin();
    }

    /**
     * Obtiene un laboratorio por su ID.
     */
    public function buscarPorId(int $id): ?Laboratorio
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea un nuevo laboratorio.
     */
    public function crear(
        string $nombre,
        string $ubicacion,
        string $descripcion,
        int $capacidad,
        ?string $especialidadPrioritaria
    ): void {

        [$nombre, $ubicacion, $descripcion, $especialidadPrioritaria] =
            $this->validar(
                $nombre,
                $ubicacion,
                $descripcion,
                $capacidad,
                $especialidadPrioritaria
            );

        if ($this->repository->findByNombre($nombre)) {
            throw new InvalidArgumentException(
                'Ya existe un laboratorio con ese nombre.'
            );
        }

        $laboratorio = new Laboratorio(
            nombre: $nombre,
            ubicacion: $ubicacion,
            descripcion: $descripcion,
            capacidad: $capacidad,
            especialidad_prioritaria: $especialidadPrioritaria,
            activo: true
        );

        $this->repository->create($laboratorio);
    }

    /**
     * Actualiza un laboratorio existente.
     */
    public function actualizar(
        int $id,
        string $nombre,
        string $ubicacion,
        string $descripcion,
        int $capacidad,
        ?string $especialidadPrioritaria,
        bool $activo
    ): void {

        $laboratorio = $this->repository->findById($id);

        if (!$laboratorio) {
            throw new InvalidArgumentException(
                'El laboratorio no existe.'
            );
        }

        [$nombre, $ubicacion, $descripcion, $especialidadPrioritaria] =
            $this->validar(
                $nombre,
                $ubicacion,
                $descripcion,
                $capacidad,
                $especialidadPrioritaria
            );

        $existente = $this->repository->findByNombre($nombre);

        if ($existente && $existente->id_laboratorio !== $id) {
            throw new InvalidArgumentException(
                'Ya existe un laboratorio con ese nombre.'
            );
        }

        $laboratorio->nombre = $nombre;
        $laboratorio->ubicacion = $ubicacion;
        $laboratorio->descripcion = $descripcion;
        $laboratorio->capacidad = $capacidad;
        $laboratorio->especialidad_prioritaria = $especialidadPrioritaria;
        $laboratorio->activo = $activo;

        $this->repository->update($laboratorio);
    }

    /**
     * Activa o desactiva un laboratorio.
     */
    public function cambiarEstado(int $id, bool $activo): void
    {
        if (!$this->repository->findById($id)) {
            throw new InvalidArgumentException(
                'El laboratorio no existe.'
            );
        }

        $this->repository->changeStatus($id, $activo);
    }

    /**
     * Valida y normaliza los datos de un laboratorio.
     *
     * @return array{0: string, 1: string, 2: string, 3: ?string}
     */
    private function validar(
        string $nombre,
        string $ubicacion,
        string $descripcion,
        int $capacidad,
        ?string $especialidadPrioritaria
    ): array {

        $nombre = trim($nombre);

        if ($nombre === '') {
            throw new InvalidArgumentException(
                'El nombre del laboratorio es obligatorio.'
            );
        }

        if (mb_strlen($nombre) > 100) {
            throw new InvalidArgumentException(
                'El nombre no puede superar los 100 caracteres.'
            );
        }

        $ubicacion = trim($ubicacion);

        if ($ubicacion === '') {
            throw new InvalidArgumentException(
                'La ubicación del laboratorio es obligatoria.'
            );
        }

        if (mb_strlen($ubicacion) > 150) {
            throw new InvalidArgumentException(
                'La ubicación no puede superar los 150 caracteres.'
            );
        }

        if ($capacidad <= 0) {
            throw new InvalidArgumentException(
                'La capacidad debe ser un número mayor a cero.'
            );
        }

        if ($capacidad > 32767) {
            throw new InvalidArgumentException(
                'La capacidad ingresada es demasiado alta.'
            );
        }

        $descripcion = trim($descripcion);

        $especialidadPrioritaria = $especialidadPrioritaria !== null
            ? trim($especialidadPrioritaria)
            : null;

        if ($especialidadPrioritaria === '') {
            $especialidadPrioritaria = null;
        }

        if (
            $especialidadPrioritaria !== null &&
            mb_strlen($especialidadPrioritaria) > 100
        ) {
            throw new InvalidArgumentException(
                'La especialidad prioritaria no puede superar los 100 caracteres.'
            );
        }

        return [$nombre, $ubicacion, $descripcion, $especialidadPrioritaria];
    }
}