<?php

declare(strict_types=1);

$title = $title ?? 'Nuevo Curso';

?>

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-12 col-lg-8 col-xl-6">

            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Nuevo Curso
                    </h2>

                    <p class="text-muted mb-0">
                        Complete la información para registrar un nuevo curso.
                    </p>

                </div>

                <a href="/cursos" class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left me-2"></i>

                    Volver

                </a>

            </div>

            <!-- Formulario -->
            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <form action="/cursos" method="POST">

                        <?= \Core\Csrf::field() ?>

                        <div class="mb-4">

                            <label
                                for="nombre"
                                class="form-label fw-semibold">

                                Nombre del Curso

                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                class="form-control form-control-lg"
                                placeholder="Ej.: Primero Medio A"
                                maxlength="50"
                                required>

                            <div class="form-text">

                                Máximo 50 caracteres.

                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">

                            <a
                                href="/cursos"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-x-circle me-2"></i>

                                Cancelar

                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-check-circle me-2"></i>

                                Guardar Curso

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>