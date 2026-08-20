<?php

declare(strict_types=1);

use Core\Session;

?>

<div class="login-page">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-12 col-md-8 col-lg-6 col-xl-5">

                <div class="card login-card border-0 shadow-lg overflow-hidden">

                    <div class="login-form">

                        <div class="text-center mb-5">

                            <i class="bi bi-shield-lock login-avatar"></i>

                            <h2 class="fw-bold mt-3">
                                Actualice su contraseña
                            </h2>

                            <p class="text-muted">
                                Por seguridad, debe definir una nueva contraseña antes de continuar.
                            </p>

                        </div>

                        <?php if (Session::hasFlash('error')): ?>

                            <div class="alert alert-danger alert-dismissible fade show" role="alert">

                                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                                <?= htmlspecialchars((string) Session::getFlash('error')) ?>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                                </button>

                            </div>

                        <?php endif; ?>

                        <form method="POST" action="/cambiar-password">

                            <?= \Core\Csrf::field() ?>

                            <div class="mb-4">

                                <label class="form-label">
                                    Nueva contraseña
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Mínimo 8 caracteres"
                                        minlength="8"
                                        required
                                        autofocus>

                                </div>

                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    Confirmar contraseña
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>

                                    <input
                                        type="password"
                                        name="password_confirm"
                                        class="form-control"
                                        placeholder="Repita la contraseña"
                                        minlength="8"
                                        required>

                                </div>

                            </div>

                            <button class="btn btn-primary btn-lg w-100">

                                <i class="bi bi-check-circle me-2"></i>

                                Guardar y continuar

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
