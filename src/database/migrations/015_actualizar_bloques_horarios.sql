/*
 * Reemplaza los bloques "dobles" (~90 min) por un bloque por cada
 * período real de clases del establecimiento, para permitir
 * reservar periodos individuales de 30-45 minutos.
 *
 * Los bloques anteriores se desactivan (no se eliminan) para
 * conservar el nombre/horario correcto en el historial de reservas
 * ya realizadas. Se libera su número de "bloque" para que los
 * nuevos bloques puedan reutilizarlo sin chocar con la restricción
 * UNIQUE.
 */

UPDATE horarios
SET
    activo = FALSE,
    bloque = NULL,
    updated_at = CURRENT_TIMESTAMP
WHERE activo = TRUE;

INSERT INTO horarios (nombre, hora_inicio, hora_fin, bloque)
VALUES
    ('Primer Bloque',   '08:00:00', '08:45:00', 1),
    ('Segundo Bloque',  '08:45:00', '09:30:00', 2),
    ('Tercer Bloque',   '09:50:00', '10:35:00', 3),
    ('Cuarto Bloque',   '10:35:00', '11:20:00', 4),
    ('Quinto Bloque',   '11:35:00', '12:05:00', 5),
    ('Sexto Bloque',    '13:50:00', '14:35:00', 6),
    ('Séptimo Bloque',  '14:35:00', '15:20:00', 7),
    ('Octavo Bloque',   '15:30:00', '16:15:00', 8);
