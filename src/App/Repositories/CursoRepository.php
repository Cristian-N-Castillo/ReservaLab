<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Curso;
use Core\Database;
use PDO;

final class CursoRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * Obtiene todos los cursos.
     *
     * @return Curso[]
     */
    public function findAll(): array
    {
        $sql = "
            SELECT *
            FROM cursos
            ORDER BY nombre ASC
        ";

        $stmt = $this->db->query($sql);

        $cursos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cursos[] = $this->hydrate($row);
        }

        return $cursos;
    }

    /**
     * Busca un curso por ID.
     */
    public function findById(int $id): ?Curso
    {
        $sql = "
            SELECT *
            FROM cursos
            WHERE id_curso = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $curso = $stmt->fetch(PDO::FETCH_ASSOC);

        return $curso ? $this->hydrate($curso) : null;
    }

    /**
     * Busca un curso por nombre.
     */
    public function findByNombre(string $nombre): ?Curso
    {
        $sql = "
            SELECT *
            FROM cursos
            WHERE LOWER(nombre) = LOWER(:nombre)
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'nombre' => $nombre
        ]);

        $curso = $stmt->fetch(PDO::FETCH_ASSOC);

        return $curso ? $this->hydrate($curso) : null;
    }

    /**
     * Inserta un nuevo curso.
     */
    public function create(Curso $curso): bool
    {
        $sql = "
            INSERT INTO cursos
            (
                nombre,
                activo
            )
            VALUES
            (
                :nombre,
                :activo
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nombre' => $curso->nombre,
            'activo' => $curso->activo
        ]);
    }

    /**
     * Actualiza un curso.
     */
    public function update(Curso $curso): bool
    {
        $sql = "
            UPDATE cursos
            SET
                nombre = :nombre,
                activo = :activo,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_curso = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $curso->id_curso,
            'nombre' => $curso->nombre,
            'activo' => $curso->activo
        ]);
    }

    /**
     * Eliminación lógica.
     */
    public function delete(int $id): bool
    {
        $sql = "
            UPDATE cursos
            SET
                activo = FALSE,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_curso = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }

    /**
     * Cuenta cursos activos.
     */
    public function count(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM cursos
            WHERE activo = TRUE
        ";

        return (int) $this->db->query($sql)->fetchColumn();
    }

    /**
     * Convierte un registro en un objeto Curso.
     */
    private function hydrate(array $row): Curso
    {
        return new Curso(
            id_curso: (int) $row['id_curso'],
            nombre: $row['nombre'],
            activo: (bool) $row['activo'],
            created_at: $row['created_at'],
            updated_at: $row['updated_at']
        );
    }
}