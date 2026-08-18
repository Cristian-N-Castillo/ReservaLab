<?php

declare(strict_types=1);

namespace App\Models;

final class Rol
{
    public function __construct(
        public ?int $id_rol = null,
        public string $nombre = '',
        public string $descripcion = '',
        public bool $activo = true,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {
    }
}