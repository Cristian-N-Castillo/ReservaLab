<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class AgendaReservaRepository extends Repository
{
    /**
     * Obtiene todos los bloques horarios junto con su disponibilidad
     * para un laboratorio y una fecha determinada.
     */
    public function obtenerBloques(
        string $fecha,
        int $idLaboratorio
    ): array {

        $sql = "
            SELECT
                h.id_horario,
                h.bloque,
                h.nombre,
                h.hora_inicio,
                h.hora_fin,

                CASE
                    WHEN r.id_reserva IS NULL THEN FALSE
                    ELSE TRUE
                END AS reservado,

                r.id_reserva,

                u.nombres,
                u.apellidos,

                c.nombre AS curso,

                er.nombre AS estado

            FROM horarios h

            LEFT JOIN reservas r
                ON r.id_horario = h.id_horario
               AND r.fecha = :fecha
               AND r.id_laboratorio = :laboratorio

            LEFT JOIN usuarios u
                ON u.id_usuario = r.id_usuario

            LEFT JOIN cursos c
                ON c.id_curso = r.id_curso

            LEFT JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE h.activo = TRUE

            ORDER BY h.bloque;
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'fecha' => $fecha,
            'laboratorio' => $idLaboratorio
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las reservas de una fecha.
     */
    public function obtenerReservas(
        string $fecha
    ): array {

        $sql = "
            SELECT

                r.id_reserva,
                r.id_usuario,

                l.nombre AS laboratorio,

                h.bloque,
                h.hora_inicio,
                h.hora_fin,

                c.nombre AS curso,

                u.nombres,
                u.apellidos,

                er.nombre AS estado,

                r.motivo

            FROM reservas r

            INNER JOIN usuarios u
                ON u.id_usuario = r.id_usuario

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN laboratorios l
                ON l.id_laboratorio = r.id_laboratorio

            INNER JOIN horarios h
                ON h.id_horario = r.id_horario

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE r.fecha = :fecha

            ORDER BY
                l.nombre,
                h.bloque;
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'fecha' => $fecha
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}