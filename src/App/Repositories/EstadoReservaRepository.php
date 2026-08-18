<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class EstadoReservaRepository extends Repository
{
    /**
     * Busca un estado de reserva por su nombre.
     */
    public function findByNombre(string $nombre): ?array
    {
        $sql = "
            SELECT
                id_estado,
                nombre,
                activo
            FROM estados_reserva
            WHERE LOWER(nombre) = LOWER(:nombre)
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'nombre' => $nombre
        ]);

        $estado = $statement->fetch(PDO::FETCH_ASSOC);

        return $estado ?: null;
    }
}