<?php

declare(strict_types=1);

use Core\Session;

/** @var string $identificador */

?>

<div class="login-page">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-12 col-md-8 col-lg-6 col-xl-5">

                <div class="card login-card border-0 shadow-lg overflow-hidden">

                    <div class="login-form">

                        <div class="text-center mb-5">

                            <i class="bi bi-question-circle login-avatar"></i>

                            <h2 class="fw-bold mt-3">
                                Recuperar contraseña
                            </h2>

                            <p class="text-muted">
                                Ingresa tu RUT o correo y te enviaremos un código de verificación.
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

                        <form method="POST" action="/recuperar-password">

                            <?= \Core\Csrf::field() ?>

                            <div class="mb-4">

                                <label class="form-label">
                                    RUT o correo
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person-vcard"></i>
                                    </span>

                                    <input
                                        type="text"
                                        name="identificador"
                                        class="form-control"
                                        placeholder="12.345.678-9 o correo@ejemplo.com"
                                        value="<?= htmlspecialchars($identificador) ?>"
                                        required
                                        autofocus>

                                </div>

                            </div>

                            <button class="btn btn-primary btn-lg w-100">

                                <i class="bi bi-send me-2"></i>

                                Enviar código

                            </button>

                            <div class="text-center mt-4">

                                <a href="/" class="text-decoration-none">

                                    <i class="bi bi-arrow-left me-1"></i>

                                    Volver a iniciar sesión

                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
