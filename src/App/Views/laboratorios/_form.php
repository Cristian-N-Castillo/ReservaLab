<?php

declare(strict_types=1);

/** @var App\Models\Laboratorio|null $laboratorio */

$laboratorio ??= new \App\Models\Laboratorio();

?>

<div class="row">

    <div class="col-md-6 mb-3">

        <label for="nombre" class="form-label">

            Nombre

        </label>

        <input
            type="text"
            id="nombre"
            name="nombre"
            class="form-control"
            value="<?= htmlspecialchars($laboratorio->nombre) ?>"
            maxlength="100"
            placeholder="Ej.: Lab Docencia"
            required>

    </div>

    <div class="col-md-6 mb-3">

        <label for="ubicacion" class="form-label">

            Ubicación

        </label>

        <input
            type="text"
            id="ubicacion"
            name="ubicacion"
            class="form-control"
            value="<?= htmlspecialchars($laboratorio->ubicacion) ?>"
            maxlength="150"
            required>

    </div>

</div>

<div class="row">

    <div class="col-md-6 mb-3">

        <label for="capacidad" class="form-label">

            Capacidad

        </label>

        <input
            type="number"
            id="capacidad"
            name="capacidad"
            class="form-control"
            value="<?= $laboratorio->capacidad > 0 ? (int) $laboratorio->capacidad : '' ?>"
            min="1"
            max="32767"
            required>

    </div>

    <div class="col-md-6 mb-3">

        <label for="especialidad_prioritaria" class="form-label">

            Especialidad prioritaria

        </label>

        <input
            type="text"
            id="especialidad_prioritaria"
            name="especialidad_prioritaria"
            class="form-control"
            value="<?= htmlspecialchars($laboratorio->especialidad_prioritaria ?? '') ?>"
            maxlength="100"
            placeholder="Opcional">

    </div>

</div>

<div class="row">

    <div class="col-12 mb-3">

        <label for="descripcion" class="form-label">

            Descripción

        </label>

        <textarea
            id="descripcion"
            name="descripcion"
            class="form-control"
            rows="3"
            placeholder="Opcional"><?= htmlspecialchars($laboratorio->descripcion) ?></textarea>

    </div>

</div>

<?php if ($laboratorio->id_laboratorio !== null): ?>

    <div class="row">

        <div class="col-12 mb-3">

            <div class="form-check form-switch">

                <input
                    type="checkbox"
                    class="form-check-input"
                    id="activo"
                    name="activo"
                    value="1"
                    <?= $laboratorio->activo ? 'checked' : '' ?>>

                <label class="form-check-label" for="activo">

                    Laboratorio activo

                </label>

            </div>

        </div>

    </div>

<?php endif; ?>

<hr>

<div class="d-flex justify-content-between">

    <a
        href="/laboratorios"
        class="btn btn-secondary">

        <i class="bi bi-arrow-left"></i>

        Volver

    </a>

    <button
        type="submit"
        class="btn btn-primary">

        <i class="bi bi-floppy"></i>

        <?= $laboratorio->id_laboratorio === null
            ? 'Guardar Laboratorio'
            : 'Actualizar Laboratorio' ?>

    </button>

</div>
