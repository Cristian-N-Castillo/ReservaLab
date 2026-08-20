<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AgendaReservaService;
use App\Services\CursoService;
use App\Services\HorarioService;
use App\Services\LaboratorioService;
use App\Services\ReservaService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use InvalidArgumentException;
use Throwable;

final class ReservaController extends Controller
{
    private ReservaService $reservaService;
    private AgendaReservaService $agendaService;
    private CursoService $cursoService;
    private HorarioService $horarioService;
    private LaboratorioService $laboratorioService;

    public function __construct()
    {
        $this->reservaService = new ReservaService();
        $this->agendaService = new AgendaReservaService();
        $this->cursoService = new CursoService();
        $this->horarioService = new HorarioService();
        $this->laboratorioService = new LaboratorioService();
    }

    /**
     * Muestra la pantalla principal de reservas.
     */
    public function index(): string
    {
        $request = new Request();

        /*
         * Fecha que se mostrará en la agenda.
         *
         * Si viene por GET usamos esa fecha.
         * De lo contrario utilizamos la fecha actual.
         */
        $fecha = trim(
            (string) $request->input(
                'fecha',
                date('Y-m-d')
            )
        );

        /*
         * Obtenemos laboratorios activos.
         */
        $laboratorios = $this->laboratorioService->listar();

        /*
         * Laboratorio seleccionado.
         *
         * Si viene por GET utilizamos ese laboratorio.
         * Si no, utilizamos el primero disponible.
         */
        $idLaboratorio = (int) $request->input(
            'id_laboratorio',
            0
        );

        if ($idLaboratorio <= 0 && !empty($laboratorios)) {
            $idLaboratorio = (int) $laboratorios[0]->id_laboratorio;
        }

        /*
         * Bloques disponibles para la fecha
         * y laboratorio seleccionado.
         */
        $bloques = [];

        if ($idLaboratorio > 0) {
            $bloques = $this->agendaService->obtenerBloques(
                $fecha,
                $idLaboratorio
            );
        }

        /*
         * Reservas existentes para la fecha seleccionada.
         */
        $reservas = $this->agendaService->obtenerReservas(
            $fecha
        );

        return $this->view(
            'reservas.index',
            [
                'title' => 'Reservas',

                'fecha' => $fecha,

                'idLaboratorioSeleccionado' => $idLaboratorio,

                'cursos' => $this->cursoService->obtenerTodos(),

                'horarios' => $this->horarioService->listar(),

                'laboratorios' => $laboratorios,

                'bloques' => $bloques,

                'reservas' => $reservas,
            ]
        );
    }

    /**
     * Muestra el calendario mensual de reservas, coloreadas
     * según su estado.
     */
    public function calendario(): string
    {
        date_default_timezone_set('America/Santiago');

        $request = new Request();

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

        /*
         * La grilla siempre muestra semanas completas (lunes a
         * domingo), incluyendo días del mes anterior/siguiente
         * para rellenar la primera y última semana.
         */
        $inicioGrilla = $primerDiaMes->modify(
            '-' . ((int) $primerDiaMes->format('N') - 1) . ' days'
        );

        $finGrilla = $ultimoDiaMes->modify(
            '+' . (7 - (int) $ultimoDiaMes->format('N')) . ' days'
        );

        $reservas = $this->reservaService->porRangoFechas(
            $inicioGrilla->format('Y-m-d'),
            $finGrilla->format('Y-m-d')
        );

        $reservasPorDia = [];

        foreach ($reservas as $reserva) {
            $reservasPorDia[$reserva['fecha']][] = $reserva;
        }

        $hoy = (new \DateTimeImmutable('today'))->format('Y-m-d');

        $semanas = [];
        $cursor = $inicioGrilla;

        while ($cursor <= $finGrilla) {

            $semana = [];

            for ($i = 0; $i < 7; $i++) {

                $fechaTexto = $cursor->format('Y-m-d');

                $semana[] = [
                    'fecha' => $fechaTexto,
                    'dia' => (int) $cursor->format('j'),
                    'esMesActual' => $cursor->format('n') === $primerDiaMes->format('n'),
                    'esHoy' => $fechaTexto === $hoy,
                    'reservas' => $reservasPorDia[$fechaTexto] ?? [],
                ];

                $cursor = $cursor->modify('+1 day');
            }

            $semanas[] = $semana;
        }

        $mesAnterior = $primerDiaMes->modify('-1 month');
        $mesSiguiente = $primerDiaMes->modify('+1 month');

        return $this->view(
            'reservas.calendario',
            [
                'title' => 'Calendario de Reservas',
                'semanas' => $semanas,
                'nombreMes' => $primerDiaMes,
                'hoy' => $hoy,
                'anioAnterior' => (int) $mesAnterior->format('Y'),
                'mesAnterior' => (int) $mesAnterior->format('n'),
                'anioSiguiente' => (int) $mesSiguiente->format('Y'),
                'mesSiguiente' => (int) $mesSiguiente->format('n'),
            ]
        );
    }

    /**
     * Guarda una nueva reserva.
     */
    public function store(): never
    {
        $request = new Request();
        $response = new Response();

        /*
         * Guardamos estos valores para poder volver
         * a la misma agenda después del POST.
         */
        $fecha = trim(
            (string) $request->input(
                'fecha',
                ''
            )
        );

        $idLaboratorio = (int) $request->input(
            'id_laboratorio',
            0
        );

        try {

            /*
             * Usuario autenticado.
             */
            $idUsuario = (int) Session::get(
                'usuario_id',
                0
            );

            if ($idUsuario <= 0) {
                throw new InvalidArgumentException(
                    'No se pudo identificar al usuario autenticado.'
                );
            }

            /*
             * Datos de la reserva.
             */
            $idCurso = (int) $request->input(
                'id_curso',
                0
            );

            $idHorario = (int) $request->input(
                'id_horario',
                0
            );

            $motivo = trim(
                (string) $request->input(
                    'motivo',
                    ''
                )
            );

            /*
             * Crear reserva.
             */
            $this->reservaService->crear(
                idUsuario: $idUsuario,
                idCurso: $idCurso,
                idLaboratorio: $idLaboratorio,
                idHorario: $idHorario,
                fecha: $fecha,
                motivo: $motivo
            );

            Session::flash(
                'success',
                'La reserva fue creada correctamente.'
            );

        } catch (InvalidArgumentException $exception) {

            Session::flash(
                'error',
                $exception->getMessage()
            );

        } catch (Throwable $exception) {

            /*
             * Durante desarrollo mostramos el error real.
             *
             * Antes de producción cambiaremos esto
             * por un mensaje genérico y logging interno.
             */
            Session::flash(
                'error',
                $exception->getMessage()
            );
        }

        /*
         * Volvemos a la misma fecha y laboratorio
         * que el usuario estaba consultando.
         */
        $query = http_build_query([
            'fecha' => $fecha,
            'id_laboratorio' => $idLaboratorio,
        ]);

        $response->redirect(
            '/reservas?' . $query
        );
    }
}