<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Usuario;
use PDO;

final class UsuarioRepository extends Repository
{
    /**
     * Obtiene el correo de todos los administradores activos
     * (se usa para notificarles eventos del sistema, como que un
     * docente confirmó una reserva).
     *
     * @return string[]
     */
    public function findCorreosAdministradores(): array
    {
        $sql = "
            SELECT u.correo
            FROM usuarios u
            INNER JOIN roles r
                ON r.id_rol = u.id_rol
            WHERE LOWER(r.nombre) = 'administrador'
              AND u.activo = TRUE
        ";

        $statement = $this->db->query($sql);

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene todos los usuarios.
     *
     * @return Usuario[]
     */
    public function findAll(): array
    {
        $sql = "
            SELECT
                u.*,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r
                ON r.id_rol = u.id_rol
            ORDER BY u.apellidos, u.nombres
        ";

        $statement = $this->db->query($sql);

        $usuarios = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = $this->hydrate($row);
        }

        return $usuarios;
    }

    public function findById(int $id): ?Usuario
    {
        $sql = "
            SELECT
                u.*,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r
                ON r.id_rol = u.id_rol
            WHERE u.id_usuario = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $usuario = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return null;
        }

        return $this->hydrate($usuario);
    }

    public function findByRut(string $rut): ?Usuario
    {
        $sql = "
            SELECT
                u.*,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r
                ON r.id_rol = u.id_rol
            WHERE u.rut = :rut
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'rut' => $rut
        ]);

        $usuario = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return null;
        }

        return $this->hydrate($usuario);
    }

    public function findByCorreo(string $correo): ?Usuario
    {
        $sql = "
            SELECT
                u.*,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r
                ON r.id_rol = u.id_rol
            WHERE u.correo = :correo
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'correo' => $correo
        ]);

        $usuario = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return null;
        }

        return $this->hydrate($usuario);
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM usuarios";

        return (int) $this->db
            ->query($sql)
            ->fetchColumn();
    }

    public function create(Usuario $usuario): void
    {
        $sql = "
            INSERT INTO usuarios
            (
                id_rol,
                rut,
                nombres,
                apellidos,
                correo,
                telefono,
                password,
                activo
            )
            VALUES
            (
                :id_rol,
                :rut,
                :nombres,
                :apellidos,
                :correo,
                :telefono,
                :password,
                :activo
            )
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id_rol'    => $usuario->id_rol,
            'rut'       => $usuario->rut,
            'nombres'   => $usuario->nombres,
            'apellidos' => $usuario->apellidos,
            'correo'    => $usuario->correo,
            'telefono'  => $usuario->telefono,
            'password'  => $usuario->password,
            'activo'    => $usuario->activo,
        ]);
    }

    public function update(Usuario $usuario): void
{
    $sql = "
        UPDATE usuarios
        SET
            id_rol = :id_rol,
            rut = :rut,
            nombres = :nombres,
            apellidos = :apellidos,
            correo = :correo,
            telefono = :telefono,
            activo = :activo,
            updated_at = NOW()
        WHERE id_usuario = :id
    ";

    $statement = $this->db->prepare($sql);

    $statement->execute([
        'id' => $usuario->id_usuario,
        'id_rol' => $usuario->id_rol,
        'rut' => $usuario->rut,
        'nombres' => $usuario->nombres,
        'apellidos' => $usuario->apellidos,
        'correo' => $usuario->correo,
        'telefono' => $usuario->telefono,
        'activo' => $usuario->activo,
    ]);
}

    /**
     * Actualiza la contraseña de un usuario y limpia el requisito
     * de cambio obligatorio (se usa tanto en el primer inicio de
     * sesión como en un futuro cambio de contraseña propio).
     */
    public function actualizarPassword(int $id, string $passwordHash): void
    {
        $sql = "
            UPDATE usuarios
            SET
                password = :password,
                debe_cambiar_password = FALSE,
                updated_at = NOW()
            WHERE id_usuario = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'password' => $passwordHash,
            'id' => $id
        ]);
    }

    /**
     * Guarda el avatar (emoji) elegido por el propio usuario.
     * Se mantiene separado de update() para que la edición de un
     * usuario desde el panel de Administrador nunca pueda borrar
     * el avatar elegido por su dueño.
     */
    public function actualizarAvatar(int $id, string $avatar): void
    {
        $sql = "
            UPDATE usuarios
            SET
                avatar = :avatar,
                updated_at = NOW()
            WHERE id_usuario = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'avatar' => $avatar !== '' ? $avatar : null,
            'id' => $id
        ]);
    }

    /**
     * Genera un código numérico de 6 dígitos para recuperar la
     * contraseña, válido por 15 minutos. Se guarda solo su hash
     * (nunca el código en texto plano) y se reinician los intentos.
     */
    public function generarCodigoReset(int $id): string
    {
        $codigo = str_pad(
            (string) random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );

        $sql = "
            UPDATE usuarios
            SET reset_codigo = :codigo,
                reset_codigo_expira = NOW() + INTERVAL '15 minutes',
                reset_intentos = 0
            WHERE id_usuario = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'codigo' => hash('sha256', $codigo),
            'id' => $id
        ]);

        return $codigo;
    }

    /**
     * Obtiene el hash del código de recuperación vigente, su
     * expiración y los intentos fallidos, para validar un intento
     * de restablecimiento de contraseña.
     */
    public function buscarCodigoReset(int $id): ?array
    {
        $sql = "
            SELECT reset_codigo, reset_codigo_expira, reset_intentos
            FROM usuarios
            WHERE id_usuario = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function incrementarIntentosReset(int $id): void
    {
        $sql = "
            UPDATE usuarios
            SET reset_intentos = reset_intentos + 1
            WHERE id_usuario = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);
    }

    /**
     * Invalida el código de recuperación (se usa tras un
     * restablecimiento exitoso o al agotar los intentos).
     */
    public function limpiarCodigoReset(int $id): void
    {
        $sql = "
            UPDATE usuarios
            SET reset_codigo = NULL,
                reset_codigo_expira = NULL,
                reset_intentos = 0
            WHERE id_usuario = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);
    }

    public function changeStatus(
        int $id,
        bool $activo
    ): void {
        $sql = "
            UPDATE usuarios
            SET
                activo = :activo,
                updated_at = NOW()
            WHERE id_usuario = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id,
            'activo' => $activo
        ]);
    }
    public function delete(int $id): void
{
    $sql = "
        DELETE FROM usuarios
        WHERE id_usuario = :id
    ";

    $statement = $this->db->prepare($sql);

    $statement->execute([
        'id' => $id
    ]);
}

    /**
     * Convierte un registro de la BD en un objeto Usuario.
     */
    private function hydrate(array $row): Usuario
    {
        return new Usuario(
            id_usuario: (int) $row['id_usuario'],
            id_rol: (int) $row['id_rol'],
            rol_nombre: $row['rol_nombre'] ?? '',
            rut: $row['rut'],
            nombres: $row['nombres'],
            apellidos: $row['apellidos'],
            correo: $row['correo'],
            telefono: $row['telefono'],
            password: $row['password'],
            avatar: $row['avatar'] ?? '',
            activo: (bool) $row['activo'],
            debe_cambiar_password: (bool) $row['debe_cambiar_password'],
            ultimo_login: $row['ultimo_login'],
            created_at: $row['created_at'],
            updated_at: $row['updated_at']
        );
    }
}