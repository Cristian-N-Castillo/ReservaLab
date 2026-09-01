<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\CursoService;
use App\Services\DashboardService;
use App\Services\ReservaService;
use App\Services\UsuarioService;
use Core\Controller;
use Core\Session;

final class DashboardController extends Controller
{
    private UsuarioService $usuarioService;
    private CursoService $cursoService;
    private DashboardService $dashboardService;
    private ReservaService $reservaService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();

        $this->cursoService = new CursoService();

        $this->dashboardService = new DashboardService();

        $this->reservaService = new ReservaService();
    }

    /**
     * Muestra el Dashboard principal.
     *
     * El Administrador ve estadísticas globales del sistema.
     * El Docente ve su propio historial de reservas.
     */
    public function index(): string
    {
        $esAdmin = (int) Session::get('id_rol', 0) === 1;

        if (!$esAdmin) {
            return $this->indexDocente();
        }

        return $this->view(
            'dashboard.index',
            [
                'title' => 'Dashboard',

                'totalUsuarios' =>
                    $this->usuarioService
                        ->cantidadUsuarios(),

                'totalCursos' =>
                    $this->cursoService
                        ->total(),

                'totalLaboratorios' =>
                    $this->dashboardService
                        ->totalLaboratorios(),

                'totalReservas' =>
                    $this->dashboardService
                        ->totalReservasHoy(),

                'proximasReservas' =>
                    $this->dashboardService
                        ->proximasReservas(5),

                'laboratoriosDashboard' =>
                    $this->dashboardService
                        ->laboratorios(),
            ]
        );
    }

    private function indexDocente(): string
    {
        $idUsuario = (int) Session::get('usuario_id', 0);

        /*
         * El Dashboard del docente solo muestra el mes en curso (se
         * "reinicia" visualmente cada mes). Esto no borra ni afecta
         * los datos: el historial completo sigue existiendo en la
         * base de datos, solo se filtra esta consulta puntual.
         */
        $primerDiaMes = date('Y-m-01');
        $ultimoDiaMes = date('Y-m-t');

        $historial = $this->reservaService->historialPorUsuario(
            $idUsuario,
            $primerDiaMes,
            $ultimoDiaMes
        );

        $hoy = date('Y-m-d');

        $proximas = array_values(array_filter(
            $historial,
            static fn (array $r): bool =>
                $r['fecha'] >= $hoy && strtolower((string) $r['estado']) !== 'cancelada'
        ));

        $canceladas = array_values(array_filter(
            $historial,
            static fn (array $r): bool => strtolower((string) $r['estado']) === 'cancelada'
        ));

        return $this->view(
            'dashboard.docente',
            [
                'title' => 'Dashboard',
                'historial' => $historial,
                'totalReservas' => count($historial),
                'totalProximas' => count($proximas),
                'totalCanceladas' => count($canceladas),
                'mostrarTutorial' => !in_array(
                    'dashboard',
                    Session::get('tutoriales_vistos', []),
                    true
                ),
            ]
        );
    }
}