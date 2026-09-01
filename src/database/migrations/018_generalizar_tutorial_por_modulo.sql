/*
 * Generaliza el tutorial guiado de bienvenida (antes solo existía
 * para el Dashboard) para que cada módulo del sistema pueda tener
 * el suyo, sin necesitar una columna nueva por cada módulo futuro.
 *
 * tutoriales_vistos guarda una lista separada por comas de las
 * claves de módulo ya vistas (ej: "dashboard,reservas").
 */

ALTER TABLE usuarios ADD COLUMN tutoriales_vistos VARCHAR(255) NOT NULL DEFAULT '';

UPDATE usuarios SET tutoriales_vistos = 'dashboard' WHERE tutorial_visto = TRUE;

ALTER TABLE usuarios DROP COLUMN tutorial_visto;
