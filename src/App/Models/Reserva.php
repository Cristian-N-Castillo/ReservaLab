<?php

declare(strict_types=1);

namespace App\Models;

final class Reserva
{
    public function __construct(
        public ?int $id_reserva = null,
        public int $id_usuario = 0,
        public int $id_curso = 0,
        public int $id_laboratorio = 0,
        public int $id_horario = 0,
        public int $id_estado = 0,
        public string $fecha = '',
        public string $motivo = '',
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {
    }
}