<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\LaboratorioRepository;
use App\Repositories\ReservaRepository;

final class DashboardService
{
    private ReservaRepository $reservaRepository;
    private LaboratorioRepository $laboratorioRepository;

    public function __construct()
    {
        $this->reservaRepository = new ReservaRepository();
        $this->laboratorioRepository = new LaboratorioRepository();
    }

    /**
     * Cantidad de reservas confirmadas para hoy.
     */
    public function totalReservasHoy(): int
    {
        return $this->reservaRepository->countReservasHoy();
    }

    /**
     * Obtiene las próximas reservas.
     */
    public function proximasReservas(
        int $limite = 5
    ): array {
        return $this->reservaRepository->findProximas(
            $limite
        );
    }

    /**
     * Obtiene los laboratorios activos junto con
     * la información de su próxima reserva.
     */
    public function laboratorios(): array
    {
        $laboratorios =
            $this->laboratorioRepository->findAll();

        $resultado = [];

        foreach ($laboratorios as $laboratorio) {

            $resultado[] = [
                'laboratorio' => $laboratorio,

                'proxima_reserva' =>
                    $this->reservaRepository
                        ->findProximaPorLaboratorio(
                            (int) $laboratorio->id_laboratorio
                        )
            ];
        }

        return $resultado;
    }

    /**
     * Cantidad de laboratorios activos.
     */
    public function totalLaboratorios(): int
    {
        return count(
            $this->laboratorioRepository->findAll()
        );
    }
}