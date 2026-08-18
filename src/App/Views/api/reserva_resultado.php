<?php

declare(strict_types=1);

/**
 * @var bool $exito
 * @var string $mensaje
 */

?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ReservaLab</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body style="background-color:#f4f6f9;">

<div class="d-flex align-items-center justify-content-center" style="min-height:100vh;padding:24px;">
<div class="card shadow-sm border-0" style="max-width:420px;width:100%;">
<div class="card-body text-center p-4">

<i class="bi <?= $exito ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>" style="font-size:3rem;"></i>

<h4 class="fw-bold mt-3 mb-2">
<?= $exito ? 'Listo' : 'No se pudo procesar' ?>
</h4>

<p class="text-muted mb-0">
<?= htmlspecialchars($mensaje) ?>
</p>

</div>
</div>
</div>

</body>
</html>
