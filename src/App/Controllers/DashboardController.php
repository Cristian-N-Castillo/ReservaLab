<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\CursoService;
use App\Services\DashboardService;
use App\Services\ReservaService;
use App\Services\UsuarioService;
use Core\Controller;
use Core\Request;
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
        date_default_timezone_set('America/Santiago');

        $idUsuario = (int) Session::get('usuario_id', 0);

        $request = new Request();

        /*
         * El Dashboard del docente muestra un mes a la vez, como un
         * horario: tanto las estadísticas como la grilla siempre
         * corresponden al mes que se está viendo. Esto no borra ni
         * afecta los datos: el historial completo sigue existiendo
         * en la base de datos, solo se filtran estas consultas.
         */
        $anio = (int) $request->input('anio', (int) date('Y'));
        $mes = (int) $request->input('mes', (int) date('n'));

        if ($mes < 1 || $mes > 12) {
            $mes = (int) date('n');
        }

        $primerDiaMes = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            sprintf('%04d-%02d-01', $anio, $mes)
        );

        if ($primerDiaMes === false) {
            $primerDiaMes = new \DateTimeImmutable('first day of this month');
        }

        $ultimoDiaMes = $primerDiaMes->modify('last day of this month');

        $historial = $this->reservaService->historialPorUsuario(
            $idUsuario,
            $primerDiaMes->format('Y-m-d'),
            $ultimoDiaMes->format('Y-m-d')
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

        /*
         * La grilla arranca el lunes de la primera semana y termina
         * el domingo de la última, para no cortar semanas por la
         * mitad. Después solo se pintan los días hábiles: no se
         * puede reservar sábado ni domingo, así que mostrarlos solo
         * ocupa espacio.
         */
        $inicioGrilla = $primerDiaMes->modify(
            '-' . ((int) $primerDiaMes->format('N') - 1) . ' days'
        );

        $finGrilla = $ultimoDiaMes->modify(
            '+' . (7 - (int) $ultimoDiaMes->format('N')) . ' days'
        );

        /*
         * En el horario solo tienen sentido las reservas vigentes:
         * las canceladas siguen contándose en la tarjeta de
         * estadísticas, pero no se pintan en la grilla.
         */
        $reservasGrilla = array_values(array_filter(
            $this->reservaService->historialPorUsuario(
                $idUsuario,
                $inicioGrilla->format('Y-m-d'),
                $finGrilla->format('Y-m-d')
            ),
            static fn (array $r): bool => in_array(
                mb_strtolower((string) $r['estado']),
                ['pendiente', 'confirmada'],
                true
            )
        ));

        $reservasPorDia = [];

        foreach ($reservasGrilla as $reserva) {
            $reservasPorDia[$reserva['fecha']][] = $reserva;
        }

        /*
         * historialPorUsuario() ordena de más reciente a más antiguo
         * (pensado para una lista), pero en el horario cada día debe
         * verse en orden cronológico: el primer bloque arriba.
         */
        foreach ($reservasPorDia as &$reservasDelDia) {

            usort(
                $reservasDelDia,
                static fn (array $a, array $b): int =>
                    ((string) $a['hora_inicio']) <=> ((string) $b['hora_inicio'])
            );
        }

        unset($reservasDelDia);

        $semanas = [];
        $cursor = $inicioGrilla;

        while ($cursor <= $finGrilla) {

            $semana = [];

            for ($i = 0; $i < 7; $i++) {

                $fechaTexto = $cursor->format('Y-m-d');

                // Solo lunes (1) a viernes (5); el fin de semana se omite.
                if ((int) $cursor->format('N') <= 5) {

                    $semana[] = [
                        'fecha' => $fechaTexto,
                        'dia' => (int) $cursor->format('j'),
                        'esMesActual' => $cursor->format('n') === $primerDiaMes->format('n'),
                        'esHoy' => $fechaTexto === $hoy,
                        'reservas' => $reservasPorDia[$fechaTexto] ?? [],
                    ];
                }

                $cursor = $cursor->modify('+1 day');
            }

            $semanas[] = $semana;
        }

        $mesAnterior = $primerDiaMes->modify('-1 month');
        $mesSiguiente = $primerDiaMes->modify('+1 month');

        return $this->view(
            'dashboard.docente',
            [
                'title' => 'Dashboard',
                'totalReservas' => count($historial),
                'totalProximas' => count($proximas),
                'totalCanceladas' => count($canceladas),
                'semanas' => $semanas,
                'nombreMes' => $primerDiaMes,
                'hoy' => $hoy,
                'anioAnterior' => (int) $mesAnterior->format('Y'),
                'mesAnterior' => (int) $mesAnterior->format('n'),
                'anioSiguiente' => (int) $mesSiguiente->format('Y'),
                'mesSiguiente' => (int) $mesSiguiente->format('n'),
                'mostrarTutorial' => !in_array(
                    'dashboard',
                    Session::get('tutoriales_vistos', []),
                    true
                ),
            ]
        );
    }
}