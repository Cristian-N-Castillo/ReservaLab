<?php

declare(strict_types=1);

use Core\Session;

if (Session::hasFlash('success')) : ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <?= htmlspecialchars((string) Session::getFlash('success')) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php endif; ?>

<?php if (Session::hasFlash('error')) : ?>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <?= htmlspecialchars((string) Session::getFlash('error')) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php endif; ?>