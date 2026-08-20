<?php

declare(strict_types=1);

/** @var string $title */
/** @var App\Models\Usuario $usuario */

?>

<div class="container">

    <div class="row justify-content-center">

        <div class="col-12 col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h3 class="mb-0">

                        <i class="bi bi-person-circle me-2"></i>

                        <?= htmlspecialchars($title) ?>

                    </h3>

                </div>

                <div class="card-body">

                    <form
                        action="/perfil"
                        method="POST">

                        <?= \Core\Csrf::field() ?>

                        <div class="row">

                            <div class="col-12 col-md-6 mb-3">

                                <label class="form-label">
                                    RUT
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($usuario->rut) ?>"
                                    disabled>

                            </div>

                            <div class="col-12 col-md-6 mb-3">

                                <label class="form-label">
                                    Rol
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($usuario->rol_nombre) ?>"
                                    disabled>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12 mb-3">

                                <?php require dirname(__DIR__) . '/partials/avatar_picker.php'; ?>

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
                                    maxlength="100"
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
                                    maxlength="100"
                                    required>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12 col-md-6 mb-3">

                                <label for="correo" class="form-label">
                                    Correo
                                </label>

                                <input
                                    type="email"
                                    id="correo"
                                    name="correo"
                                    class="form-control"
                                    value="<?= htmlspecialchars($usuario->correo) ?>"
                                    maxlength="150"
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
                                    value="<?= htmlspecialchars($usuario->telefono) ?>"
                                    maxlength="20"
                                    placeholder="Opcional">

                            </div>

                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">

                            <a
                                href="/dashboard"
                                class="btn btn-secondary">

                                <i class="bi bi-arrow-left"></i>

                                Volver

                            </a>

                            <div class="d-flex gap-2">

                                <a
                                    href="/cambiar-password"
                                    class="btn btn-outline-primary">

                                    <i class="bi bi-key"></i>

                                    Cambiar contraseña

                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-primary">

                                    <i class="bi bi-floppy"></i>

                                    Guardar cambios

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>
