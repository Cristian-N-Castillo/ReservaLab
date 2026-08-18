<?php

declare(strict_types=1);

namespace App\Models;

final class Curso
{
    public function __construct(

        public ?int $id_curso = null,

        public string $nombre = '',

        public bool $activo = true,

        public ?string $created_at = null,

        public ?string $updated_at = null

    ) {
    }
}