<?php

declare(strict_types=1);

namespace App\Models;

final class Laboratorio
{
    public function __construct(
        public ?int $id_laboratorio = null,
        public string $nombre = '',
        public string $ubicacion = '',
        public string $descripcion = '',
        public int $capacidad = 0,
        public ?string $especialidad_prioritaria = null,
        public bool $activo = true,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {
    }
}