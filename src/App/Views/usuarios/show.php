<h1><?= htmlspecialchars($title ?? 'Usuario') ?></h1>

<ul>

    <li>RUT: <?= htmlspecialchars($usuario->rut) ?></li>

    <li>Nombres: <?= htmlspecialchars($usuario->nombres) ?></li>

    <li>Apellidos: <?= htmlspecialchars($usuario->apellidos) ?></li>

    <li>Correo: <?= htmlspecialchars($usuario->correo) ?></li>

    <li>Teléfono: <?= htmlspecialchars($usuario->telefono) ?></li>

</ul>