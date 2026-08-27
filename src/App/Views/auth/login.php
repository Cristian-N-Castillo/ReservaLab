<?php

declare(strict_types=1);

use Core\Session;

?>

<div class="login-page">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-12 col-xl-10">

                <div class="card login-card border-0 shadow-lg overflow-hidden">

                    <div class="row g-0">

                        <!-- Panel Izquierdo -->

                        <div class="col-lg-6 login-left d-none d-lg-flex">

                            <div class="w-100 text-center">

                                <img
                                    src="<?= \Core\Asset::url('/assets/img/logo-sna-huerton.png') ?>"
                                    alt="Liceo El Huerton"
                                    class="login-logo mb-4">

                                <h1 class="fw-bold mb-3">

                                    ReservaLab

                                </h1>

                                <p class="lead">

                                    Sistema de Reserva de Laboratorios

                                </p>

                                <hr class="my-4 opacity-25">

                                <p>

                                    Administra usuarios, laboratorios, horarios y reservas desde una única plataforma.

                                </p>

                            </div>

                        </div>

                        <!-- Panel Derecho -->

                        <div class="col-12 col-lg-6">

                            <div class="login-form">

                                <div class="text-center mb-5">

                                    <i class="bi bi-person-circle login-avatar"></i>

                                    <h2 class="fw-bold mt-3">

                                        Bienvenido

                                    </h2>

                                    <p class="text-muted">

                                        Inicia sesión para continuar

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

                                <form method="POST" action="/login">

                                    <?= \Core\Csrf::field() ?>

                                    <div class="mb-4">

                                        <label class="form-label">

                                            RUT

                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">

                                                <i class="bi bi-person-vcard"></i>

                                            </span>

                                            <input
                                                type="text"
                                                name="rut"
                                                class="form-control"
                                                placeholder="12.345.678-9"
                                                required
                                                autofocus>

                                        </div>

                                    </div>

                                    <div class="mb-4">

                                        <label class="form-label">

                                            Contraseña

                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">

                                                <i class="bi bi-lock-fill"></i>

                                            </span>

                                            <input
                                                type="password"
                                                name="password"
                                                class="form-control"
                                                placeholder="********"
                                                required>

                                        </div>

                                    </div>

                                    <div class="text-end mb-4">

                                        <a href="/recuperar-password" class="text-decoration-none small">

                                            ¿Olvidaste tu contraseña?

                                        </a>

                                    </div>

                                    <button class="btn btn-primary btn-lg w-100">

                                        <i class="bi bi-box-arrow-in-right me-2"></i>

                                        Iniciar sesión

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>