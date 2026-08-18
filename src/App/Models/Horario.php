<?php

declare(strict_types=1);

namespace App\Models;

final class Horario
{
    public function __construct(
        public ?int $id_horario = null,
        public string $nombre = '',
        public string $hora_inicio = '',
        public string $hora_fin = '',
        public bool $activo = true,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {
    }
}