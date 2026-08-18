ALTER TABLE usuarios ADD COLUMN debe_cambiar_password BOOLEAN NOT NULL DEFAULT TRUE;

-- Los usuarios ya existentes no deben verse forzados a cambiar su
-- contraseña actual; el requisito solo aplica a cuentas nuevas
-- creadas a partir de esta versión.
UPDATE usuarios SET debe_cambiar_password = FALSE;
