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
<td style="background-color:#198754;padding:20px 24px;">
<span style="color:#ffffff;font-size:18px;font-weight:bold;">ReservaLab</span>
</td>
</tr>

<tr>
<td style="padding:24px;">

<p style="margin:0 0 16px;font-size:14px;color:#495057;line-height:1.5;">
El docente <strong><?= htmlspecialchars($nombres . ' ' . $apellidos) ?></strong> confirmó la siguiente reserva:
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fa;border-radius:6px;margin-bottom:4px;">
<tr>
<td style="padding:16px;font-size:14px;color:#212529;">
<p style="margin:0 0 6px;"><strong>Laboratorio:</strong> <?= htmlspecialchars($laboratorio) ?></p>
<p style="margin:0 0 6px;"><strong>Curso:</strong> <?= htmlspecialchars($curso) ?></p>
<p style="margin:0 0 6px;"><strong>Fecha:</strong> <?= htmlspecialchars($fecha) ?></p>
<p style="margin:0;"><strong>Horario:</strong> <?= htmlspecialchars($horario) ?> (<?= htmlspecialchars(substr((string) $hora_inicio, 0, 5)) ?> - <?= htmlspecialchars(substr((string) $hora_fin, 0, 5)) ?>)</p>
</td>
</tr>
</table>

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
