<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Laboratorio;
use PDO;

final class LaboratorioRepository extends Repository
{
    /**
     * Obtiene todos los laboratorios activos.
     *
     * @return Laboratorio[]
     */
    public function findAll(): array
    {
        $sql = "
            SELECT *
            FROM laboratorios
            WHERE activo = TRUE
            ORDER BY nombre
        ";

        $statement = $this->db->query($sql);

        $laboratorios = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $laboratorios[] = $this->hydrate($row);
        }

        return $laboratorios;
    }

    /**
     * Busca un laboratorio por su ID.
     */
    public function findById(int $id): ?Laboratorio
    {
        $sql = "
            SELECT *
            FROM laboratorios
            WHERE id_laboratorio = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Obtiene todos los laboratorios, activos e inactivos.
     *
     * Utilizado en la pantalla de administración.
     *
     * @return Laboratorio[]
     */
    public function findAllAdmin(): array
    {
        $sql = "
            SELECT *
            FROM laboratorios
            ORDER BY nombre
        ";

        $statement = $this->db->query($sql);

        $laboratorios = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $laboratorios[] = $this->hydrate($row);
        }

        return $laboratorios;
    }

    /**
     * Busca un laboratorio por su nombre.
     */
    public function findByNombre(string $nombre): ?Laboratorio
    {
        $sql = "
            SELECT *
            FROM laboratorios
            WHERE LOWER(nombre) = LOWER(:nombre)
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'nombre' => $nombre
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Inserta un nuevo laboratorio.
     */
    public function create(Laboratorio $laboratorio): bool
    {
        $sql = "
            INSERT INTO laboratorios
            (
                nombre,
                ubicacion,
                descripcion,
                capacidad,
                especialidad_prioritaria,
                activo
            )
            VALUES
            (
                :nombre,
                :ubicacion,
                :descripcion,
                :capacidad,
                :especialidad_prioritaria,
                :activo
            )
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            'nombre' => $laboratorio->nombre,
            'ubicacion' => $laboratorio->ubicacion,
            'descripcion' => $laboratorio->descripcion !== '' ? $laboratorio->descripcion : null,
            'capacidad' => $laboratorio->capacidad,
            'especialidad_prioritaria' => $laboratorio->especialidad_prioritaria,
            'activo' => (int) $laboratorio->activo,
        ]);
    }

    /**
     * Actualiza un laboratorio existente.
     */
    public function update(Laboratorio $laboratorio): bool
    {
        $sql = "
            UPDATE laboratorios
            SET
                nombre = :nombre,
                ubicacion = :ubicacion,
                descripcion = :descripcion,
                capacidad = :capacidad,
                especialidad_prioritaria = :especialidad_prioritaria,
                activo = :activo,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_laboratorio = :id
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            'id' => $laboratorio->id_laboratorio,
            'nombre' => $laboratorio->nombre,
            'ubicacion' => $laboratorio->ubicacion,
            'descripcion' => $laboratorio->descripcion !== '' ? $laboratorio->descripcion : null,
            'capacidad' => $laboratorio->capacidad,
            'especialidad_prioritaria' => $laboratorio->especialidad_prioritaria,
            'activo' => (int) $laboratorio->activo,
        ]);
    }

    /**
     * Activa o desactiva un laboratorio.
     */
    public function changeStatus(int $id, bool $activo): bool
    {
        $sql = "
            UPDATE laboratorios
            SET
                activo = :activo,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_laboratorio = :id
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            'id' => $id,
            'activo' => (int) $activo,
        ]);
    }

    /**
     * Convierte un registro de la base de datos en un objeto Laboratorio.
     */
    private function hydrate(array $row): Laboratorio
    {
        return new Laboratorio(
            id_laboratorio: (int) $row['id_laboratorio'],
            nombre: $row['nombre'],
            ubicacion: $row['ubicacion'] ?? '',
            capacidad: (int) ($row['capacidad'] ?? 0),
            descripcion: $row['descripcion'] ?? '',
            especialidad_prioritaria: $row['especialidad_prioritaria'] ?? null,
            activo: (bool) $row['activo'],
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }
}