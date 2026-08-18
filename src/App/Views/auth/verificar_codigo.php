<?php

declare(strict_types=1);

use Core\Session;

?>

<div class="login-page">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-xl-5 col-lg-6 col-md-8">

                <div class="card login-card border-0 shadow-lg overflow-hidden">

                    <div class="login-form">

                        <div class="text-center mb-5">

                            <i class="bi bi-shield-lock login-avatar"></i>

                            <h2 class="fw-bold mt-3">
                                Verifica tu código
                            </h2>

                            <p class="text-muted">
                                Ingresa el código que te enviamos por correo y define tu nueva contraseña.
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

                        <?php if (Session::hasFlash('success')): ?>

                            <div class="alert alert-success alert-dismissible fade show" role="alert">

                                <i class="bi bi-check-circle-fill me-2"></i>

                                <?= htmlspecialchars((string) Session::getFlash('success')) ?>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                                </button>

                            </div>

                        <?php endif; ?>

                        <form method="POST" action="/recuperar-password/verificar">

                            <?= \Core\Csrf::field() ?>

                            <div class="mb-4">

                                <label class="form-label">
                                    Código de verificación
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-hash"></i>
                                    </span>

                                    <input
                                        type="text"
                                        name="codigo"
                                        class="form-control"
                                        placeholder="123456"
                                        inputmode="numeric"
                                        maxlength="6"
                                        required
                                        autofocus>

                                </div>

                            </div>

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
                                        required>

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

                                Restablecer contraseña

                            </button>

                            <div class="text-center mt-4">

                                <a href="/recuperar-password" class="text-decoration-none">

                                    <i class="bi bi-arrow-left me-1"></i>

                                    ¿No recibiste el código? Solicitar uno nuevo

                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
