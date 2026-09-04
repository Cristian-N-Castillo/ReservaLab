<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ReservaRepository;
use InvalidArgumentException;

final class ReporteService
{
    private ReservaRepository $repository;

    public function __construct()
    {
        $this->repository = new ReservaRepository();
    }

    /**
     * Genera los datos de todos los reportes definidos en el
     * alcance funcional, dentro de un rango de fechas opcional.
     */
    public function generar(?string $desde, ?string $hasta): array
    {
        $desde = $this->normalizarFecha($desde, 'Fecha desde');
        $hasta = $this->normalizarFecha($hasta, 'Fecha hasta');

        if ($desde !== null && $hasta !== null && $desde > $hasta) {
            throw new InvalidArgumentException(
                'La fecha desde no puede ser posterior a la fecha hasta.'
            );
        }

        $porLaboratorio = $this->repository->porLaboratorio($desde, $hasta);

        return [
            'por_laboratorio' => $porLaboratorio,
            'por_docente' => $this->repository->porDocente($desde, $hasta),
            'por_curso' => $this->repository->porCurso($desde, $hasta),
            'mas_utilizados' => array_slice($porLaboratorio, 0, 5),
        ];
    }

    /**
     * Valida y normaliza una fecha en formato Y-m-d.
     */
    private function normalizarFecha(?string $fecha, string $etiqueta): ?string
    {
        $fecha = trim((string) $fecha);

        if ($fecha === '') {
            return null;
        }

        $fechaObjeto = \DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);

        if ($fechaObjeto === false || $fechaObjeto->format('Y-m-d') !== $fecha) {
            throw new InvalidArgumentException(
                "{$etiqueta} no es válida."
            );
        }

        return $fecha;
    }
}
