<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reserva;
use PDO;

final class ReservaRepository extends Repository
{
    /**
     * Obtiene todas las reservas.
     *
     * @return Reserva[]
     */
    public function findAll(): array
    {
        $sql = "
            SELECT *
            FROM reservas
            ORDER BY fecha DESC, id_horario ASC
        ";

        $statement = $this->db->query($sql);

        $reservas = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $reservas[] = $this->hydrate($row);
        }

        return $reservas;
    }

    /**
     * Busca una reserva por su ID.
     */
    public function findById(int $id): ?Reserva
    {
        $sql = "
            SELECT *
            FROM reservas
            WHERE id_reserva = :id
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
     * Verifica si un bloque ya se encuentra reservado
     * para una fecha y laboratorio determinados.
     *
     * Las reservas canceladas no bloquean el horario.
     */
    public function existeReserva(
        string $fecha,
        int $idLaboratorio,
        int $idHorario
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM reservas r

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE r.fecha = :fecha
              AND r.id_laboratorio = :laboratorio
              AND r.id_horario = :horario
              AND LOWER(er.nombre) <> 'cancelada'
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'fecha' => $fecha,
            'laboratorio' => $idLaboratorio,
            'horario' => $idHorario
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * Guarda una nueva reserva y retorna su ID.
     */
    public function create(Reserva $reserva): int
    {
        $sql = "
            INSERT INTO reservas
            (
                id_usuario,
                id_curso,
                id_laboratorio,
                id_horario,
                id_estado,
                fecha,
                motivo
            )
            VALUES
            (
                :id_usuario,
                :id_curso,
                :id_laboratorio,
                :id_horario,
                :id_estado,
                :fecha,
                :motivo
            )
            RETURNING id_reserva
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id_usuario' => $reserva->id_usuario,
            'id_curso' => $reserva->id_curso,
            'id_laboratorio' => $reserva->id_laboratorio,
            'id_horario' => $reserva->id_horario,
            'id_estado' => $reserva->id_estado,
            'fecha' => $reserva->fecha,
            'motivo' => $reserva->motivo
        ]);

        return (int) $statement->fetchColumn();
    }

    /**
     * Genera un token de un solo uso para confirmar/cancelar la reserva
     * por correo, válido hasta el final del día de la reserva.
     */
    public function generarToken(int $id, string $fecha): string
    {
        $token = bin2hex(random_bytes(32));

        $sql = "
            UPDATE reservas
            SET token_confirmacion = :token,
                token_expira = (:fecha::date + INTERVAL '1 day')
            WHERE id_reserva = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'token' => $token,
            'fecha' => $fecha,
            'id' => $id
        ]);

        return $token;
    }

    /**
     * Obtiene el estado y el token vigente de una reserva,
     * para validar un enlace de confirmación/cancelación.
     */
    public function buscarToken(int $id): ?array
    {
        $sql = "
            SELECT id_reserva, id_estado, token_confirmacion, token_expira
            FROM reservas
            WHERE id_reserva = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Cambia el estado de una reserva.
     */
    public function actualizarEstado(int $id, int $idEstado): void
    {
        $sql = "
            UPDATE reservas
            SET id_estado = :id_estado,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_reserva = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id_estado' => $idEstado,
            'id' => $id
        ]);
    }

    /**
     * Marca como Finalizada toda reserva Pendiente o Confirmada
     * cuyo bloque horario ya haya terminado (fecha pasada, o fecha
     * de hoy con hora_fin ya cumplida). Las canceladas no se tocan.
     *
     * Retorna la cantidad de reservas finalizadas.
     */
    public function finalizarVencidas(
        int $idEstadoFinalizada,
        string $hoy,
        string $horaActual
    ): int {
        $sql = "
            UPDATE reservas r
            SET id_estado = :id_estado_finalizada,
                updated_at = CURRENT_TIMESTAMP
            FROM horarios h, estados_reserva er
            WHERE r.id_horario = h.id_horario
              AND r.id_estado = er.id_estado
              AND LOWER(er.nombre) IN ('pendiente', 'confirmada')
              AND (
                    r.fecha < :hoy
                    OR (r.fecha = :hoy AND h.hora_fin <= :hora_actual)
                  )
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id_estado_finalizada' => $idEstadoFinalizada,
            'hoy' => $hoy,
            'hora_actual' => $horaActual,
        ]);

        return $statement->rowCount();
    }

    /**
     * Invalida el token de confirmación de una reserva
     * (se usa tras confirmarla o cancelarla, para que el
     * enlace del correo no pueda reutilizarse).
     */
    public function invalidarToken(int $id): void
    {
        $sql = "
            UPDATE reservas
            SET token_confirmacion = NULL,
                token_expira = NULL
            WHERE id_reserva = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);
    }

    /**
     * Cuenta las reservas pendientes correspondientes
     * a la fecha actual.
     */
    public function countReservasHoy(): int
    {
        $sql = "
            SELECT COUNT(*)

            FROM reservas r

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE r.fecha = CURRENT_DATE
              AND LOWER(er.nombre) = 'pendiente'
        ";

        $statement = $this->db->query($sql);

        return (int) $statement->fetchColumn();
    }

    /**
     * Obtiene las próximas reservas desde la fecha actual.
     *
     * No incluye reservas canceladas.
     */
    public function findProximas(int $limite = 5): array
    {
        $limite = max(1, min($limite, 20));

        $sql = "
            SELECT
                r.id_reserva,
                r.fecha,
                r.motivo,

                h.id_horario,
                h.bloque,
                h.nombre AS horario,
                h.hora_inicio,
                h.hora_fin,

                l.id_laboratorio,
                l.nombre AS laboratorio,

                c.id_curso,
                c.nombre AS curso,

                u.id_usuario,
                u.nombres,
                u.apellidos,

                er.id_estado,
                er.nombre AS estado

            FROM reservas r

            INNER JOIN horarios h
                ON h.id_horario = r.id_horario

            INNER JOIN laboratorios l
                ON l.id_laboratorio = r.id_laboratorio

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN usuarios u
                ON u.id_usuario = r.id_usuario

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE r.fecha >= CURRENT_DATE
              AND LOWER(er.nombre) NOT IN ('cancelada', 'finalizada')

            ORDER BY
                r.fecha ASC,
                h.hora_inicio ASC

            LIMIT {$limite}
        ";

        $statement = $this->db->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una reserva junto con la información legible
     * de docente, curso, laboratorio, horario y estado.
     */
    public function findDetalladaById(int $id): ?array
    {
        $sql = "
            SELECT
                r.id_reserva,
                r.fecha,
                r.motivo,

                h.id_horario,
                h.nombre AS horario,
                h.hora_inicio,
                h.hora_fin,

                l.id_laboratorio,
                l.nombre AS laboratorio,

                c.id_curso,
                c.nombre AS curso,

                u.id_usuario,
                u.nombres,
                u.apellidos,
                u.correo,

                er.id_estado,
                er.nombre AS estado

            FROM reservas r

            INNER JOIN horarios h
                ON h.id_horario = r.id_horario

            INNER JOIN laboratorios l
                ON l.id_laboratorio = r.id_laboratorio

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN usuarios u
                ON u.id_usuario = r.id_usuario

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE r.id_reserva = :id

            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Obtiene la próxima reserva de un laboratorio.
     *
     * Se utilizará en las tarjetas del Dashboard.
     */
    public function findProximaPorLaboratorio(
        int $idLaboratorio
    ): ?array {
        $sql = "
            SELECT
                r.id_reserva,
                r.fecha,

                h.bloque,
                h.nombre AS horario,
                h.hora_inicio,
                h.hora_fin,

                c.nombre AS curso,

                er.nombre AS estado

            FROM reservas r

            INNER JOIN horarios h
                ON h.id_horario = r.id_horario

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE r.id_laboratorio = :id_laboratorio
              AND r.fecha >= CURRENT_DATE
              AND LOWER(er.nombre) NOT IN ('cancelada', 'finalizada')

            ORDER BY
                r.fecha ASC,
                h.hora_inicio ASC

            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id_laboratorio' => $idLaboratorio
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Reservas agrupadas por laboratorio, dentro de un rango de fechas opcional.
     *
     * No incluye reservas canceladas.
     */
    public function porLaboratorio(?string $desde, ?string $hasta): array
    {
        $sql = "
            SELECT
                l.id_laboratorio,
                l.nombre AS laboratorio,
                COUNT(*) AS total

            FROM reservas r

            INNER JOIN laboratorios l
                ON l.id_laboratorio = r.id_laboratorio

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE LOWER(er.nombre) <> 'cancelada'
              AND (:desde::date IS NULL OR r.fecha >= :desde)
              AND (:hasta::date IS NULL OR r.fecha <= :hasta)

            GROUP BY l.id_laboratorio, l.nombre
            ORDER BY total DESC, l.nombre
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'desde' => $desde,
            'hasta' => $hasta,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reservas agrupadas por docente, dentro de un rango de fechas opcional.
     *
     * No incluye reservas canceladas.
     */
    public function porDocente(?string $desde, ?string $hasta): array
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.nombres,
                u.apellidos,
                COUNT(*) AS total

            FROM reservas r

            INNER JOIN usuarios u
                ON u.id_usuario = r.id_usuario

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE LOWER(er.nombre) <> 'cancelada'
              AND (:desde::date IS NULL OR r.fecha >= :desde)
              AND (:hasta::date IS NULL OR r.fecha <= :hasta)

            GROUP BY u.id_usuario, u.nombres, u.apellidos
            ORDER BY total DESC, u.apellidos
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'desde' => $desde,
            'hasta' => $hasta,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reservas agrupadas por curso, dentro de un rango de fechas opcional.
     *
     * No incluye reservas canceladas.
     */
    public function porCurso(?string $desde, ?string $hasta): array
    {
        $sql = "
            SELECT
                c.id_curso,
                c.nombre AS curso,
                COUNT(*) AS total

            FROM reservas r

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE LOWER(er.nombre) <> 'cancelada'
              AND (:desde::date IS NULL OR r.fecha >= :desde)
              AND (:hasta::date IS NULL OR r.fecha <= :hasta)

            GROUP BY c.id_curso, c.nombre
            ORDER BY total DESC, c.nombre
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'desde' => $desde,
            'hasta' => $hasta,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Historial completo de reservas dentro de un rango de fechas opcional,
     * incluyendo canceladas.
     */
    public function historial(?string $desde, ?string $hasta): array
    {
        $sql = "
            SELECT
                r.id_reserva,
                r.id_usuario,
                r.fecha,
                r.motivo,

                h.nombre AS horario,
                h.hora_inicio,
                h.hora_fin,

                l.nombre AS laboratorio,

                c.nombre AS curso,

                u.nombres,
                u.apellidos,

                er.nombre AS estado

            FROM reservas r

            INNER JOIN horarios h
                ON h.id_horario = r.id_horario

            INNER JOIN laboratorios l
                ON l.id_laboratorio = r.id_laboratorio

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN usuarios u
                ON u.id_usuario = r.id_usuario

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE (:desde::date IS NULL OR r.fecha >= :desde)
              AND (:hasta::date IS NULL OR r.fecha <= :hasta)

            ORDER BY r.fecha DESC, h.hora_inicio DESC
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'desde' => $desde,
            'hasta' => $hasta,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Historial de reservas de un docente, más recientes primero.
     *
     * $desde/$hasta son opcionales: si se omiten, trae el historial
     * completo sin límite de fecha.
     */
    public function historialPorUsuario(
        int $idUsuario,
        ?string $desde = null,
        ?string $hasta = null
    ): array {
        $sql = "
            SELECT
                r.id_reserva,
                r.fecha,
                r.motivo,

                h.nombre AS horario,
                h.hora_inicio,
                h.hora_fin,

                l.nombre AS laboratorio,

                c.nombre AS curso,

                er.nombre AS estado

            FROM reservas r

            INNER JOIN horarios h
                ON h.id_horario = r.id_horario

            INNER JOIN laboratorios l
                ON l.id_laboratorio = r.id_laboratorio

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE r.id_usuario = :id_usuario
              AND (:desde::date IS NULL OR r.fecha >= :desde)
              AND (:hasta::date IS NULL OR r.fecha <= :hasta)

            ORDER BY r.fecha DESC, h.hora_inicio DESC
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id_usuario' => $idUsuario,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reservas Pendientes cuyo correo de confirmación se envió hace
     * más de $minutos minutos y que todavía no recibieron el
     * recordatorio (se usa para avisar al docente que aún no ha
     * confirmado ni cancelado la reserva).
     */
    public function pendientesParaRecordatorio(int $minutos): array
    {
        $sql = "
            SELECT
                r.id_reserva,
                r.fecha,
                r.motivo,
                r.token_confirmacion,

                h.nombre AS horario,
                h.hora_inicio,
                h.hora_fin,

                l.nombre AS laboratorio,

                c.nombre AS curso,

                u.nombres,
                u.apellidos,
                u.correo

            FROM reservas r

            INNER JOIN horarios h
                ON h.id_horario = r.id_horario

            INNER JOIN laboratorios l
                ON l.id_laboratorio = r.id_laboratorio

            INNER JOIN cursos c
                ON c.id_curso = r.id_curso

            INNER JOIN usuarios u
                ON u.id_usuario = r.id_usuario

            INNER JOIN estados_reserva er
                ON er.id_estado = r.id_estado

            WHERE LOWER(er.nombre) = 'pendiente'
              AND r.recordatorio_enviado = FALSE
              AND r.token_confirmacion IS NOT NULL
              AND r.created_at <= NOW() - (:minutos || ' minutes')::interval

            ORDER BY r.created_at ASC
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'minutos' => $minutos
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marca que ya se envió el recordatorio de una reserva Pendiente,
     * para no volver a enviarlo.
     */
    public function marcarRecordatorioEnviado(int $id): void
    {
        $sql = "
            UPDATE reservas
            SET recordatorio_enviado = TRUE
            WHERE id_reserva = :id
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);
    }

    /**
     * Convierte un registro en un objeto Reserva.
     */
    private function hydrate(array $row): Reserva
    {
        return new Reserva(
            id_reserva: (int) $row['id_reserva'],
            id_usuario: (int) $row['id_usuario'],
            id_curso: (int) $row['id_curso'],
            id_laboratorio: (int) $row['id_laboratorio'],
            id_horario: (int) $row['id_horario'],
            id_estado: (int) $row['id_estado'],
            fecha: $row['fecha'],
            motivo: $row['motivo'] ?? '',
            created_at: $row['created_at'] ?? null,
            updated_at: $row['updated_at'] ?? null
        );
    }
}