CREATE TABLE usuarios
(
    id_usuario SERIAL PRIMARY KEY,

    id_rol INTEGER NOT NULL,

    rut VARCHAR(12) NOT NULL UNIQUE,

    nombres VARCHAR(100) NOT NULL,

    apellidos VARCHAR(100) NOT NULL,

    correo VARCHAR(150) NOT NULL UNIQUE,

    telefono VARCHAR(20),

    password VARCHAR(255) NOT NULL,

    activo BOOLEAN NOT NULL DEFAULT TRUE,

    ultimo_login TIMESTAMP,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuarios_roles
        FOREIGN KEY (id_rol)
        REFERENCES roles(id_rol)
);