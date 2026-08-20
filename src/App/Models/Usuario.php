<?php

declare(strict_types=1);

namespace App\Models;

final class Usuario
{
    public function __construct(
        public ?int $id_usuario = null,
        public int $id_rol = 2,
        public string $rol_nombre = '',
        public string $rut = '',
        public string $nombres = '',
        public string $apellidos = '',
        public string $correo = '',
        public string $telefono = '',
        public string $password = '',
        public string $avatar = '',
        public bool $activo = true,
        public bool $debe_cambiar_password = true,
        public ?string $ultimo_login = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {
    }
}