<?php

declare(strict_types=1);

use App\Services\ReservaService;

$service = new ReservaService();

$finalizadas = $service->finalizarVencidas();

echo PHP_EOL;
echo "Reservas finalizadas: {$finalizadas}" . PHP_EOL;
