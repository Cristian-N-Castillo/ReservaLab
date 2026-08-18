<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AgendaReservaRepository;

final class AgendaReservaService
{
    private AgendaReservaRepository $repository;

    public function __construct()
    {
        $this->repository = new AgendaReservaRepository();
    }

    /**
     * Obtiene los bloques horarios con su disponibilidad.
     */
    public function obtenerBloques(
        string $fecha,
        int $idLaboratorio
    ): array {
        return $this->repository->obtenerBloques(
            $fecha,
            $idLaboratorio
        );
    }

    /**
     * Obtiene todas las reservas de una fecha.
     */
    public function obtenerReservas(
        string $fecha
    ): array {
        return $this->repository->obtenerReservas($fecha);
    }
}