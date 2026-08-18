<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ReservaService;
use Core\Controller;
use InvalidArgumentException;
use Throwable;

/**
 * Endpoints públicos (sin sesión) accedidos desde los enlaces
 * del correo de confirmación de reserva. La autorización no se
 * basa en el login, sino en el token de un solo uso de la reserva.
 */
final class ReservaApiController extends Controller
{
    private ReservaService $service;

    public function __construct()
    {
        $this->service = new ReservaService();
    }

    public function confirmar(int $id, string $token): string
    {
        return $this->procesar(
            $id,
            $token,
            'confirmarPorToken',
            'La reserva fue confirmada correctamente.'
        );
    }

    public function cancelar(int $id, string $token): string
    {
        return $this->procesar(
            $id,
            $token,
            'cancelarPorToken',
            'La reserva fue cancelada correctamente.'
        );
    }

    private function procesar(int $id, string $token, string $metodo, string $mensajeExito): string
    {
        try {

            $this->service->{$metodo}($id, $token);

            return $this->view(
                'api.reserva_resultado',
                [
                    'exito' => true,
                    'mensaje' => $mensajeExito,
                ],
                null
            );

        } catch (InvalidArgumentException $e) {

            return $this->view(
                'api.reserva_resultado',
                [
                    'exito' => false,
                    'mensaje' => $e->getMessage(),
                ],
                null
            );

        } catch (Throwable $e) {

            return $this->view(
                'api.reserva_resultado',
                [
                    'exito' => false,
                    'mensaje' => 'Ocurrió un error inesperado. Intente nuevamente más tarde.',
                ],
                null
            );
        }
    }
}
