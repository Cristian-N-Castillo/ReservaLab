<?php

declare(strict_types=1);

/** @var string $title */
/** @var App\Models\Laboratorio $laboratorio */

?>

<div class="container">

    <div class="row justify-content-center">

        <div class="col-12 col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-warning">

                    <h3 class="mb-0">

                        <i class="bi bi-pencil-square me-2"></i>

                        <?= htmlspecialchars($title) ?>

                    </h3>

                </div>

                <div class="card-body">

                    <form
                        action="/laboratorios/<?= $laboratorio->id_laboratorio ?>"
                        method="POST">

                        <?= \Core\Csrf::field() ?>

                        <?php require __DIR__ . '/_form.php'; ?>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>
