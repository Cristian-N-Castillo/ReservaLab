<?php

declare(strict_types=1);

/**
 * @var string[] $avatares
 * @var string $avatarSeleccionado
 */

$avatares = $avatares ?? [];
$avatarSeleccionado = $avatarSeleccionado ?? '';

?>

<div class="avatar-picker">

    <label class="form-label fw-semibold d-block mb-2">
        Avatar
        <span class="text-muted fw-normal">(opcional)</span>
    </label>

    <div class="avatar-grid">

        <input
            type="radio"
            class="avatar-radio"
            name="avatar"
            id="avatar_ninguno"
            value=""
            <?= $avatarSeleccionado === '' ? 'checked' : '' ?>>

        <label
            for="avatar_ninguno"
            class="avatar-option"
            title="Sin avatar">

            <i class="bi bi-person-circle"></i>

        </label>

        <?php foreach ($avatares as $indice => $emoji): ?>

            <input
                type="radio"
                class="avatar-radio"
                name="avatar"
                id="avatar_<?= (int) $indice ?>"
                value="<?= htmlspecialchars($emoji) ?>"
                <?= $avatarSeleccionado === $emoji ? 'checked' : '' ?>>

            <label
                for="avatar_<?= (int) $indice ?>"
                class="avatar-option">

                <?= $emoji ?>

            </label>

        <?php endforeach; ?>

    </div>

</div>
