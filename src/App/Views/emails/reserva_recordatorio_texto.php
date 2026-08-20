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
 * @var string $urlConfirmar
 * @var string $urlCancelar
 */

?>
Estimado/a <?= $nombres . ' ' . $apellidos ?>,

Le recordamos que aún no ha confirmado ni cancelado la siguiente reserva:

Laboratorio: <?= $laboratorio ?>

Curso: <?= $curso ?>

Fecha: <?= $fecha ?>

Horario: <?= $horario ?> (<?= substr((string) $hora_inicio, 0, 5) ?> - <?= substr((string) $hora_fin, 0, 5) ?>)

Para CONFIRMAR la reserva, ingrese a:
<?= $urlConfirmar ?>

Para CANCELAR la reserva, ingrese a:
<?= $urlCancelar ?>

Este enlace es personal y de un solo uso, y expira al finalizar el día de la reserva. Si ya no necesita el laboratorio, cancele la reserva para liberar el horario a otros docentes.
