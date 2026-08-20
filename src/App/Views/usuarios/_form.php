<?php

declare(strict_types=1);

/** @var App\Models\Usuario|null $usuario */
/** @var App\Models\Rol[] $roles */

$usuario ??= new \App\Models\Usuario();

?>

<div class="row">

    <div class="col-12 col-md-6 mb-3">

        <label for="id_rol" class="form-label">

            Rol

        </label>

        <select
            name="id_rol"
            id="id_rol"
            class="form-select"
            required>

            <option value="">
                Seleccione un rol
            </option>

            <?php foreach ($roles as $rol): ?>

                <option
                    value="<?= $rol->id_rol ?>"
                    <?= $usuario->id_rol === $rol->id_rol ? 'selected' : '' ?>>

                    <?= htmlspecialchars($rol->nombre) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="col-12 col-md-6 mb-3">

        <label for="rut" class="form-label">

            RUT

        </label>

        <input
            type="text"
            id="rut"
            name="rut"
            class="form-control"
            value="<?= htmlspecialchars($usuario->rut) ?>"
            placeholder="12.345.678-9"
            required>

    </div>

</div>

<div class="row">

    <div class="col-12 col-md-6 mb-3">

        <label for="nombres" class="form-label">

            Nombres

        </label>

        <input
            type="text"
            id="nombres"
            name="nombres"
            class="form-control"
            value="<?= htmlspecialchars($usuario->nombres) ?>"
            required>

    </div>

    <div class="col-12 col-md-6 mb-3">

        <label for="apellidos" class="form-label">

            Apellidos

        </label>

        <input
            type="text"
            id="apellidos"
            name="apellidos"
            class="form-control"
            value="<?= htmlspecialchars($usuario->apellidos) ?>"
            required>

    </div>

</div>

<div class="row">

    <div class="col-12 col-md-6 mb-3">

        <label for="correo" class="form-label">

            Correo electrónico

        </label>

        <input
            type="email"
            id="correo"
            name="correo"
            class="form-control"
            value="<?= htmlspecialchars($usuario->correo) ?>"
            required>

    </div>

    <div class="col-12 col-md-6 mb-3">

        <label for="telefono" class="form-label">

            Teléfono

        </label>

        <input
            type="text"
            id="telefono"
            name="telefono"
            class="form-control"
            value="<?= htmlspecialchars($usuario->telefono) ?>">

    </div>

</div>

<?php if ($usuario->id_usuario === null): ?>

<div class="row">

    <div class="col-12 col-md-6 mb-3">

        <label for="password" class="form-label">

            Contraseña

        </label>

        <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            required>

    </div>

    <div class="col-12 col-md-6 mb-3">

        <label for="password_confirm" class="form-label">

            Confirmar contraseña

        </label>

        <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            class="form-control"
            required>

    </div>

</div>

<?php endif; ?>

<hr>

<div class="d-flex justify-content-between">

    <a
        href="/usuarios"
        class="btn btn-secondary">

        <i class="bi bi-arrow-left"></i>

        Volver

    </a>

    <button
        type="submit"
        class="btn btn-primary">

        <i class="bi bi-floppy"></i>

        <?= $usuario->id_usuario === null
            ? 'Guardar Usuario'
            : 'Actualizar Usuario' ?>

    </button>

</div>