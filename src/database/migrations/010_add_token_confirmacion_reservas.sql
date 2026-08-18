ALTER TABLE reservas ADD COLUMN token_confirmacion VARCHAR(64) NULL;
ALTER TABLE reservas ADD COLUMN token_expira TIMESTAMP NULL;
ALTER TABLE reservas ADD CONSTRAINT uq_reservas_token UNIQUE (token_confirmacion);
