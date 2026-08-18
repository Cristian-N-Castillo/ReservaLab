<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ObservacionService;
use App\Services\ReservaService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use InvalidArgumentException;
use RuntimeException;

final class ObservacionController extends Controller
{
    private ObservacionService $service;
    private ReservaService $reservaService;
    private Response $response;

    public function __construct()
    {
        $this->service = new ObservacionService();
        $this->reservaService = new ReservaService();
        $this->response = new Response();
    }

    /**
     * Listado global de observaciones registradas.
     */
    public function index(): string
    {
        return $this->view(
            'observaciones.index',
            [
                'title' => 'Observaciones',
                'observaciones' => $this->service->listar(),
            ]
        );
    }

    /**
     * Detalle de una reserva junto con sus observaciones
     * y el formulario para registrar una nueva.
     *
     * El Administrador puede ver la de cualquier reserva.
     * El Docente solo puede ver las observaciones de sus
     * propias reservas, y no ve el formulario para crear una.
     */
    public function porReserva(int $idReserva): string
    {
        $reserva = $this->reservaService->obtenerDetalle($idReserva);

        if (!$reserva) {
            Session::flash(
                'error',
                'La reserva indicada no existe.'
            );

            $this->response->redirect('/reservas');
        }

        if (!$this->puedeVerReserva((int) $reserva['id_usuario'])) {

            Session::flash(
                'error',
                'No tiene permisos para ver las observaciones de esta reserva.'
            );

            $this->response->redirect('/reservas');
        }

        return $this->view(
            'observaciones.reserva',
            [
                'title' => 'Observaciones de la Reserva',
                'reserva' => $reserva,
                'observaciones' => $this->service->listarPorReserva($idReserva),
                'esAdmin' => $this->esAdmin(),
            ]
        );
    }

    /**
     * Registra una nueva observación sobre una reserva.
     */
    public function store(int $idReserva, Request $request): void
    {
        try {

            $idUsuarioAdmin = (int) Session::get('usuario_id', 0);

            $this->service->crear(
                idReserva: $idReserva,
                idUsuarioAdmin: $idUsuarioAdmin,
                texto: (string) $request->input('observacion', ''),
                archivo: $request->file('archivo_pdf')
            );

            Session::flash(
                'success',
                'Observación registrada correctamente.'
            );

        } catch (InvalidArgumentException|RuntimeException $e) {

            Session::flash(
                'error',
                $e->getMessage()
            );
        }

        $this->response->redirect("/reservas/{$idReserva}/observaciones");
    }

    /**
     * Descarga el PDF adjunto a una observación.
     *
     * Solo puede descargarlo el Administrador o el docente
     * dueño de la reserva a la que pertenece la observación.
     */
    public function descargarPdf(int $id): never
    {
        $observacion = $this->service->obtener($id);
        $ruta = $observacion ? $this->service->rutaPdf($observacion) : null;

        if (!$observacion || $ruta === null || !is_file($ruta)) {

            http_response_code(404);

            echo 'El archivo solicitado no existe.';

            exit;
        }

        $reserva = $this->reservaService->obtener($observacion->id_reserva);

        if (!$reserva || !$this->puedeVerReserva($reserva->id_usuario)) {

            http_response_code(403);

            echo 'No tiene permisos para acceder a este archivo.';

            exit;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="observacion-' . $id . '.pdf"');
        header('Content-Length: ' . filesize($ruta));

        readfile($ruta);

        exit;
    }

    private function esAdmin(): bool
    {
        return (int) Session::get('id_rol', 0) === 1;
    }

    /**
     * Determina si el usuario en sesión puede ver la información
     * de una reserva: el Administrador siempre puede; el Docente
     * solo si es el dueño de la reserva.
     */
    private function puedeVerReserva(int $idUsuarioReserva): bool
    {
        if ($this->esAdmin()) {
            return true;
        }

        return (int) Session::get('usuario_id', 0) === $idUsuarioReserva;
    }
}
