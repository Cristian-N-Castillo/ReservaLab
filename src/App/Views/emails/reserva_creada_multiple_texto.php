<?php

declare(strict_types=1);

/**
 * @var string $nombres
 * @var string $apellidos
 * @var string $fecha
 * @var string $laboratorio
 * @var string $curso
 * @var string $motivo
 * @var array $bloques
 */

?>
Estimado/a <?= $nombres . ' ' . $apellidos ?>,

Se han registrado <?= count($bloques) ?> reservas a su nombre para el mismo laboratorio, curso y motivo. Por favor confirme o cancele cada bloque por separado utilizando los enlaces correspondientes.

Laboratorio: <?= $laboratorio ?>

Curso: <?= $curso ?>

Fecha: <?= $fecha ?>

Motivo: <?= $motivo ?>

<?php foreach ($bloques as $bloque): ?>
----------------------------------------
<?= $bloque['horario'] ?> (<?= substr((string) $bloque['hora_inicio'], 0, 5) ?> - <?= substr((string) $bloque['hora_fin'], 0, 5) ?>)

Para CONFIRMAR este bloque, ingrese a:
<?= $bloque['urlConfirmar'] ?>

Para CANCELAR este bloque, ingrese a:
<?= $bloque['urlCancelar'] ?>

<?php endforeach; ?>
----------------------------------------

Recordatorio: cuiden los equipos del laboratorio. No está permitido comer ni beber líquidos dentro de la sala.

Cada enlace es personal y de un solo uso, y expira al finalizar el día de la reserva.
