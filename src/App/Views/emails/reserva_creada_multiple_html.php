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
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial, Helvetica, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9;padding:24px 0;">
<tr>
<td align="center">

<table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08);max-width:480px;">

<tr>
<td style="background-color:#1B6B3B;padding:20px 24px;">
<span style="color:#ffffff;font-size:18px;font-weight:bold;">ReservaLab</span>
</td>
</tr>

<tr>
<td style="padding:24px;">

<p style="margin:0 0 12px;font-size:15px;color:#212529;">
Estimado/a <strong><?= htmlspecialchars($nombres . ' ' . $apellidos) ?></strong>,
</p>

<p style="margin:0 0 16px;font-size:14px;color:#495057;line-height:1.5;">
Se han registrado <?= count($bloques) ?> reservas a su nombre para el mismo laboratorio, curso y motivo. Por favor confirme o cancele cada bloque por separado utilizando los botones correspondientes.
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fa;border-radius:6px;margin-bottom:20px;">
<tr>
<td style="padding:16px;font-size:14px;color:#212529;">
<p style="margin:0 0 6px;"><strong>Laboratorio:</strong> <?= htmlspecialchars($laboratorio) ?></p>
<p style="margin:0 0 6px;"><strong>Curso:</strong> <?= htmlspecialchars($curso) ?></p>
<p style="margin:0 0 6px;"><strong>Fecha:</strong> <?= htmlspecialchars($fecha) ?></p>
<p style="margin:0;"><strong>Motivo:</strong> <?= htmlspecialchars($motivo) ?></p>
</td>
</tr>
</table>

<?php foreach ($bloques as $bloque): ?>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dee2e6;border-radius:6px;margin-bottom:16px;">
<tr>
<td style="padding:14px 16px;">

<p style="margin:0 0 12px;font-size:14px;color:#212529;">
<strong><?= htmlspecialchars((string) $bloque['horario']) ?></strong>
(<?= htmlspecialchars(substr((string) $bloque['hora_inicio'], 0, 5)) ?> - <?= htmlspecialchars(substr((string) $bloque['hora_fin'], 0, 5)) ?>)
</p>

<table role="presentation" cellpadding="0" cellspacing="0">
<tr>
<td style="padding:0 6px 0 0;">
<a href="<?= htmlspecialchars((string) $bloque['urlConfirmar']) ?>" style="display:inline-block;background-color:#198754;color:#ffffff;text-decoration:none;font-size:13px;font-weight:bold;padding:9px 16px;border-radius:6px;">Confirmar</a>
</td>
<td style="padding:0 0 0 6px;">
<a href="<?= htmlspecialchars((string) $bloque['urlCancelar']) ?>" style="display:inline-block;background-color:#dc3545;color:#ffffff;text-decoration:none;font-size:13px;font-weight:bold;padding:9px 16px;border-radius:6px;">Cancelar</a>
</td>
</tr>
</table>

</td>
</tr>
</table>

<?php endforeach; ?>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff3cd;border:1px solid #ffe69c;border-radius:6px;margin-bottom:8px;">
<tr>
<td style="padding:14px 16px;font-size:13px;color:#664d03;line-height:1.5;">
<strong>Recordatorio:</strong> cuiden los equipos del laboratorio. No está permitido comer ni beber líquidos dentro de la sala.
</td>
</tr>
</table>

<p style="margin:16px 0 0;font-size:12px;color:#868e96;">
Cada enlace es personal y de un solo uso, y expira al finalizar el día de la reserva. Si usted no realizó estas reservas, puede ignorar este correo.
</p>

</td>
</tr>

<tr>
<td style="background-color:#f1f3f5;padding:14px 24px;text-align:center;">
<span style="font-size:11px;color:#adb5bd;">ReservaLab - Sistema de Reserva de Laboratorios</span>
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
