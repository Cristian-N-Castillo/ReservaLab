<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Horario;
use PDO;

final class HorarioRepository extends Repository
{
    /**
     * @return Horario[]
     */
    public function findAll(): array
    {
        $sql = "
            SELECT
                id_horario,
                nombre,
                hora_inicio,
                hora_fin,
                activo,
                created_at,
                updated_at
            FROM horarios
            WHERE activo = TRUE
            ORDER BY hora_inicio
        ";

        $statement = $this->db->query($sql);

        $horarios = [];

        while ($row = $statement->fetch()) {

            $horarios[] = new Horario(
                id_horario: (int)$row['id_horario'],
                nombre: $row['nombre'],
                hora_inicio: $row['hora_inicio'],
                hora_fin: $row['hora_fin'],
                activo: (bool)$row['activo'],
                created_at: $row['created_at'],
                updated_at: $row['updated_at']
            );

        }

        return $horarios;
    }

    public function findById(int $id): ?Horario
    {
        $sql = "
            SELECT
                id_horario,
                nombre,
                hora_inicio,
                hora_fin,
                activo,
                created_at,
                updated_at
            FROM horarios
            WHERE id_horario = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        return new Horario(
            id_horario: (int) $row['id_horario'],
            nombre: $row['nombre'],
            hora_inicio: $row['hora_inicio'],
            hora_fin: $row['hora_fin'],
            activo: (bool) $row['activo'],
            created_at: $row['created_at'],
            updated_at: $row['updated_at']
        );
    }
}