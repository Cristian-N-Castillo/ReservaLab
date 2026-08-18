<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Observacion;
use PDO;

final class ObservacionRepository extends Repository
{
    /**
     * Obtiene todas las observaciones junto con el contexto
     * de la reserva a la que pertenecen.
     */
    public function findAll(): array
    {
        $sql = "
            SELECT
                o.id_observacion,
                o.observacion,
                o.archivo_pdf,
                o.created_at,

                r.id_reserva,
                r.fecha,

                l.nombre AS laboratorio,

                c.nombre AS curso,

                doc.nombres AS docente_nombres,
                doc.apellidos AS docente_apellidos,

                adm.nombres AS admin_nombres,
                adm.apellidos AS admin_apellidos

            FROM observaciones o

            INNER JOIN reservas r
                ON r.id_reserva = o.id_reserva

            INNER JOIN laboratorios l
                ON l.id_laboratorio = r.id_laboratorio

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN usuarios doc
                ON doc.id_usuario = r.id_usuario

            INNER JOIN usuarios adm
                ON adm.id_usuario = o.id_usuario_admin

            ORDER BY o.created_at DESC
        ";

        $statement = $this->db->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las observaciones registradas para una reserva específica.
     */
    public function findByReserva(int $idReserva): array
    {
        $sql = "
            SELECT
                o.id_observacion,
                o.observacion,
                o.archivo_pdf,
                o.created_at,

                adm.nombres AS admin_nombres,
                adm.apellidos AS admin_apellidos

            FROM observaciones o

            INNER JOIN usuarios adm
                ON adm.id_usuario = o.id_usuario_admin

            WHERE o.id_reserva = :id_reserva

            ORDER BY o.created_at DESC
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id_reserva' => $idReserva
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una observación por su ID.
     */
    public function findById(int $id): ?Observacion
    {
        $sql = "
            SELECT *
            FROM observaciones
            WHERE id_observacion = :id
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
     * Guarda una nueva observación.
     */
    public function create(Observacion $observacion): int
    {
        $sql = "
            INSERT INTO observaciones
            (
                id_reserva,
                id_usuario_admin,
                observacion,
                archivo_pdf
            )
            VALUES
            (
                :id_reserva,
                :id_usuario_admin,
                :observacion,
                :archivo_pdf
            )
            RETURNING id_observacion
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id_reserva' => $observacion->id_reserva,
            'id_usuario_admin' => $observacion->id_usuario_admin,
            'observacion' => $observacion->observacion,
            'archivo_pdf' => $observacion->archivo_pdf,
        ]);

        return (int) $statement->fetchColumn();
    }

    /**
     * Convierte un registro en un objeto Observacion.
     */
    private function hydrate(array $row): Observacion
    {
        return new Observacion(
            id_observacion: (int) $row['id_observacion'],
            id_reserva: (int) $row['id_reserva'],
            id_usuario_admin: (int) $row['id_usuario_admin'],
            observacion: $row['observacion'],
            archivo_pdf: $row['archivo_pdf'] ?? null,
            created_at: $row['created_at'] ?? null
        );
    }
}
