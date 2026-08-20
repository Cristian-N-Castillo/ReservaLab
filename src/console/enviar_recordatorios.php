<?php

declare(strict_types=1);

use App\Services\ReservaService;

$minutos = isset($argv[2]) ? (int) $argv[2] : 5;

$service = new ReservaService();

$enviados = $service->enviarRecordatoriosPendientes($minutos);

echo PHP_EOL;
echo "Recordatorios enviados: {$enviados}" . PHP_EOL;
