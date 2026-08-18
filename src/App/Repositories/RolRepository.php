<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Rol;
use PDO;

final class RolRepository extends Repository
{
    /**
     * Obtiene todos los roles activos.
     *
     * @return Rol[]
     */
    public function findAll(): array
    {
        $sql = "
            SELECT *
            FROM roles
            WHERE activo = TRUE
            ORDER BY nombre
        ";

        $statement = $this->db->query($sql);

        $roles = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $roles[] = $this->hydrate($row);
        }

        return $roles;
    }

    /**
     * Busca un rol por su ID.
     */
    public function findById(int $id): ?Rol
    {
        $sql = "
            SELECT *
            FROM roles
            WHERE id_rol = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $rol = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$rol) {
            return null;
        }

        return $this->hydrate($rol);
    }

    /**
     * Convierte un registro en un objeto Rol.
     */
    private function hydrate(array $row): Rol
    {
        return new Rol(
            id_rol: (int) $row['id_rol'],
            nombre: $row['nombre'],
            descripcion: $row['descripcion'],
            activo: (bool) $row['activo'],
            created_at: $row['created_at'],
            updated_at: $row['updated_at']
        );
    }
}