<?php

declare(strict_types=1);

namespace App\Models;

final class Observacion
{
    public function __construct(
        public ?int $id_observacion = null,
        public int $id_reserva = 0,
        public int $id_usuario_admin = 0,
        public string $observacion = '',
        public ?string $archivo_pdf = null,
        public ?string $created_at = null
    ) {
    }
}
