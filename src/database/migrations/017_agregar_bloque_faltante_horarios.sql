/*
 * Faltaba un período real de clases entre el Quinto Bloque y el
 * almuerzo: 12:05-13:05. Se inserta ese bloque y se renumeran/
 * renombran los bloques de la tarde para que sigan el orden
 * correcto (ahora son 9 períodos reales, no 8).
 *
 * El orden de los UPDATE es a propósito (de atrás hacia adelante)
 * para no chocar con la restricción UNIQUE de "bloque" mientras se
 * corren.
 */

UPDATE horarios
SET nombre = 'Noveno Bloque', bloque = 9, updated_at = CURRENT_TIMESTAMP
WHERE hora_inicio = '15:30:00' AND activo = TRUE;

UPDATE horarios
SET nombre = 'Octavo Bloque', bloque = 8, updated_at = CURRENT_TIMESTAMP
WHERE hora_inicio = '14:35:00' AND activo = TRUE;

UPDATE horarios
SET nombre = 'Séptimo Bloque', bloque = 7, updated_at = CURRENT_TIMESTAMP
WHERE hora_inicio = '13:50:00' AND activo = TRUE;

INSERT INTO horarios (nombre, hora_inicio, hora_fin, bloque)
VALUES ('Sexto Bloque', '12:05:00', '13:05:00', 6);
