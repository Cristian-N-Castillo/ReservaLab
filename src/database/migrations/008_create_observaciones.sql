CREATE TABLE observaciones
(
    id_observacion SERIAL PRIMARY KEY,

    id_reserva INTEGER NOT NULL,

    id_usuario_admin INTEGER NOT NULL,

    observacion TEXT NOT NULL,

    archivo_pdf VARCHAR(255),

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_observaciones_reserva
        FOREIGN KEY (id_reserva)
        REFERENCES reservas(id_reserva),

    CONSTRAINT fk_observaciones_admin
        FOREIGN KEY (id_usuario_admin)
        REFERENCES usuarios(id_usuario)
);