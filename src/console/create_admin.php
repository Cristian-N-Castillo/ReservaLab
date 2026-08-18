<?php

declare(strict_types=1);
use Core\Database;

$db = Database::connection();

echo PHP_EOL;

$rut = readline('RUT: ');
$nombres = readline('Nombres: ');
$apellidos = readline('Apellidos: ');
$correo = readline('Correo: ');
$telefono = readline('Teléfono: ');
$password = readline('Contraseña: ');

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$sql = <<<SQL
INSERT INTO usuarios
(
    id_rol,
    rut,
    nombres,
    apellidos,
    correo,
    telefono,
    password,
    activo
)
VALUES
(
    1,
    :rut,
    :nombres,
    :apellidos,
    :correo,
    :telefono,
    :password,
    TRUE
)
SQL;

$stmt = $db->prepare($sql);

$stmt->execute([
    'rut' => $rut,
    'nombres' => $nombres,
    'apellidos' => $apellidos,
    'correo' => $correo,
    'telefono' => $telefono,
    'password' => $passwordHash,
]);

echo PHP_EOL;
echo "✔ Administrador creado correctamente." . PHP_EOL;