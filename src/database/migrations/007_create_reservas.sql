CREATE TABLE reservas
(
    id_reserva SERIAL PRIMARY KEY,

    id_usuario INTEGER NOT NULL,

    id_curso INTEGER NOT NULL,

    id_laboratorio INTEGER NOT NULL,

    id_horario INTEGER NOT NULL,

    id_estado INTEGER NOT NULL,

    fecha DATE NOT NULL,

    motivo VARCHAR(255) NOT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservas_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario),

    CONSTRAINT fk_reservas_curso
        FOREIGN KEY (id_curso)
        REFERENCES cursos(id_curso),

    CONSTRAINT fk_reservas_laboratorio
        FOREIGN KEY (id_laboratorio)
        REFERENCES laboratorios(id_laboratorio),

    CONSTRAINT fk_reservas_horario
        FOREIGN KEY (id_horario)
        REFERENCES horarios(id_horario),

    CONSTRAINT fk_reservas_estado
        FOREIGN KEY (id_estado)
        REFERENCES estados_reserva(id_estado),

    CONSTRAINT uq_reserva
        UNIQUE (id_laboratorio, fecha, id_horario)
);