<?php

declare(strict_types=1);

/**
 * @var string $nombres
 * @var string $apellidos
 * @var string $fecha
 * @var string $horario
 * @var string $hora_inicio
 * @var string $hora_fin
 * @var string $laboratorio
 * @var string $curso
 */

?>
El docente <?= $nombres . ' ' . $apellidos ?> confirmó la siguiente reserva:

Laboratorio: <?= $laboratorio ?>

Curso: <?= $curso ?>

Fecha: <?= $fecha ?>

Horario: <?= $horario ?> (<?= substr((string) $hora_inicio, 0, 5) ?> - <?= substr((string) $hora_fin, 0, 5) ?>)
