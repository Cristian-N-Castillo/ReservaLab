<?php

declare(strict_types=1);

/**
 * @var string $nombres
 * @var string $apellidos
 * @var string $codigo
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
<td style="background-color:#0d6efd;padding:20px 24px;">
<span style="color:#ffffff;font-size:18px;font-weight:bold;">ReservaLab</span>
</td>
</tr>

<tr>
<td style="padding:24px;">

<p style="margin:0 0 12px;font-size:15px;color:#212529;">
Estimado/a <strong><?= htmlspecialchars($nombres . ' ' . $apellidos) ?></strong>,
</p>

<p style="margin:0 0 20px;font-size:14px;color:#495057;line-height:1.5;">
Recibimos una solicitud para restablecer tu contraseña. Usa el siguiente código de verificación para continuar:
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fa;border-radius:6px;margin-bottom:20px;">
<tr>
<td style="padding:20px;text-align:center;">
<span style="font-size:32px;font-weight:bold;letter-spacing:8px;color:#0d6efd;"><?= htmlspecialchars($codigo) ?></span>
</td>
</tr>
</table>

<p style="margin:0;font-size:12px;color:#868e96;">
Este código es válido por 15 minutos y solo puede utilizarse una vez. Si no solicitaste este cambio, puedes ignorar este correo.
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
