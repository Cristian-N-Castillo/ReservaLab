CREATE TABLE laboratorios
(
    id_laboratorio SERIAL PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    ubicacion VARCHAR(150) NOT NULL,

    descripcion TEXT,

    capacidad SMALLINT NOT NULL,

    especialidad_prioritaria VARCHAR(100),

    activo BOOLEAN NOT NULL DEFAULT TRUE,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);