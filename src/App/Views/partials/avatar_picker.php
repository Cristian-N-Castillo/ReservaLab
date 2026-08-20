<?php

declare(strict_types=1);

/**
 * @var array<string, string[]> $avatares
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

    <div class="avatar-scroll">

        <div class="avatar-category-title">
            General
        </div>

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

        </div>

        <?php foreach ($avatares as $categoria => $emojis): ?>

            <div class="avatar-category-title">
                <?= htmlspecialchars($categoria) ?>
            </div>

            <div class="avatar-grid">

                <?php foreach ($emojis as $emoji): ?>

                    <?php $idAvatar = 'avatar_' . bin2hex($emoji); ?>

                    <input
                        type="radio"
                        class="avatar-radio"
                        name="avatar"
                        id="<?= $idAvatar ?>"
                        value="<?= htmlspecialchars($emoji) ?>"
                        <?= $avatarSeleccionado === $emoji ? 'checked' : '' ?>>

                    <label
                        for="<?= $idAvatar ?>"
                        class="avatar-option">

                        <?= $emoji ?>

                    </label>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </div>

</div>
