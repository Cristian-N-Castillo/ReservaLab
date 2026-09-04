<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ReporteService;
use Core\Controller;
use Core\Request;
use Core\Session;
use InvalidArgumentException;

final class ReporteController extends Controller
{
    private ReporteService $service;

    public function __construct()
    {
        $this->service = new ReporteService();
    }

    public function index(Request $request): string
    {
        $desde = trim((string) $request->input('fecha_desde', ''));
        $hasta = trim((string) $request->input('fecha_hasta', ''));

        try {

            $reportes = $this->service->generar(
                $desde !== '' ? $desde : null,
                $hasta !== '' ? $hasta : null
            );

        } catch (InvalidArgumentException $e) {

            Session::flash('error', $e->getMessage());

            $reportes = $this->service->generar(null, null);

            $desde = '';
            $hasta = '';
        }

        return $this->view(
            'reportes.index',
            [
                'title' => 'Reportes',
                'fechaDesde' => $desde,
                'fechaHasta' => $hasta,
                'porLaboratorio' => $reportes['por_laboratorio'],
                'porDocente' => $reportes['por_docente'],
                'porCurso' => $reportes['por_curso'],
                'masUtilizados' => $reportes['mas_utilizados'],
            ]
        );
    }
}
