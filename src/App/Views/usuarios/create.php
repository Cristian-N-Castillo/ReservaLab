<?php

declare(strict_types=1);

use Core\Session;

/** @var string $title */
/** @var App\Models\Rol[] $roles */

?>

<div class="container">

    <div class="row justify-content-center">

        <div class="col-12 col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h3 class="mb-0">

                        <i class="bi bi-person-plus-fill me-2"></i>

                        <?= htmlspecialchars($title) ?>

                    </h3>

                </div>

                <div class="card-body">

                    <?php if (Session::hasFlash('error')): ?>

                        <div class="alert alert-danger alert-dismissible fade show">

                            <i class="bi bi-exclamation-triangle-fill me-2"></i>

                            <?= htmlspecialchars(Session::getFlash('error')) ?>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                            </button>

                        </div>

                    <?php endif; ?>

                    <form action="/usuarios" method="POST">

                        <?= \Core\Csrf::field() ?>

                        <?php require __DIR__ . '/_form.php'; ?>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>