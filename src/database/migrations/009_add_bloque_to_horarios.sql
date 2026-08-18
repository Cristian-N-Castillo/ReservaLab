ALTER TABLE horarios
    ADD COLUMN bloque SMALLINT;

ALTER TABLE horarios
    ADD CONSTRAINT horarios_bloque_unique UNIQUE (bloque);
