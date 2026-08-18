<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Reserva;
use App\Repositories\CursoRepository;
use App\Repositories\EstadoReservaRepository;
use App\Repositories\HorarioRepository;
use App\Repositories\LaboratorioRepository;
use App\Repositories\ReservaRepository;
use Core\Config;
use Core\View;
use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;

final class ReservaService
{
    /**
     * Máximo de días de anticipación permitidos para reservar.
     */
    private const DIAS_MAXIMOS_ANTICIPACION = 21;

    private ReservaRepository $repository;
    private EstadoReservaRepository $estadoRepository;
    private CursoRepository $cursoRepository;
    private LaboratorioRepository $laboratorioRepository;
    private HorarioRepository $horarioRepository;
    private NotificacionService $notificacion;

    public function __construct()
    {
        $this->repository = new ReservaRepository();
        $this->estadoRepository = new EstadoReservaRepository();
        $this->cursoRepository = new CursoRepository();
        $this->laboratorioRepository = new LaboratorioRepository();
        $this->horarioRepository = new HorarioRepository();
        $this->notificacion = new NotificacionService();
    }

    /**
     * Obtiene todas las reservas.
     *
     * @return Reserva[]
     */
    public function listar(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Busca una reserva por su ID.
     */
    public function obtener(int $id): ?Reserva
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtiene una reserva con la información legible de
     * docente, curso, laboratorio, horario y estado.
     */
    public function obtenerDetalle(int $id): ?array
    {
        return $this->repository->findDetalladaById($id);
    }

    /**
     * Historial completo de reservas de un docente.
     */
    public function historialPorUsuario(int $idUsuario): array
    {
        return $this->repository->historialPorUsuario($idUsuario);
    }

    /**
     * Crea una nueva reserva.
     */
    public function crear(
        int $idUsuario,
        int $idCurso,
        int $idLaboratorio,
        int $idHorario,
        string $fecha,
        string $motivo
    ): void {

        /*
         * =========================================================
         * VALIDACIÓN DE USUARIO
         * =========================================================
         */

        if ($idUsuario <= 0) {
            throw new InvalidArgumentException(
                'El usuario no es válido.'
            );
        }

        /*
         * =========================================================
         * VALIDACIÓN DE CURSO
         * =========================================================
         */

        if ($idCurso <= 0) {
            throw new InvalidArgumentException(
                'Debe seleccionar un curso.'
            );
        }

        $curso = $this->cursoRepository->findById($idCurso);

        if (!$curso) {
            throw new InvalidArgumentException(
                'El curso seleccionado no existe.'
            );
        }

        if (!$curso->activo) {
            throw new InvalidArgumentException(
                'El curso seleccionado se encuentra inactivo.'
            );
        }

        /*
         * =========================================================
         * VALIDACIÓN DE LABORATORIO
         * =========================================================
         */

        if ($idLaboratorio <= 0) {
            throw new InvalidArgumentException(
                'Debe seleccionar un laboratorio.'
            );
        }

        $laboratorio = $this->laboratorioRepository->findById($idLaboratorio);

        if (!$laboratorio) {
            throw new InvalidArgumentException(
                'El laboratorio seleccionado no existe.'
            );
        }

        if (!$laboratorio->activo) {
            throw new InvalidArgumentException(
                'El laboratorio seleccionado se encuentra inactivo.'
            );
        }

        /*
         * =========================================================
         * VALIDACIÓN DE BLOQUE HORARIO
         * =========================================================
         */

        if ($idHorario <= 0) {
            throw new InvalidArgumentException(
                'Debe seleccionar un bloque horario.'
            );
        }

        $horario = $this->horarioRepository->findById($idHorario);

        if (!$horario) {
            throw new InvalidArgumentException(
                'El bloque horario seleccionado no existe.'
            );
        }

        if (!$horario->activo) {
            throw new InvalidArgumentException(
                'El bloque horario seleccionado se encuentra inactivo.'
            );
        }

        /*
         * =========================================================
         * VALIDACIÓN DE FECHA
         * =========================================================
         */

        $fecha = trim($fecha);

        if ($fecha === '') {
            throw new InvalidArgumentException(
                'Debe seleccionar una fecha.'
            );
        }

        $fechaObjeto = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $fecha
        );

        if (
            $fechaObjeto === false ||
            $fechaObjeto->format('Y-m-d') !== $fecha
        ) {
            throw new InvalidArgumentException(
                'La fecha seleccionada no es válida.'
            );
        }

        $hoy = new \DateTimeImmutable('today');

        if ($fechaObjeto < $hoy) {
            throw new InvalidArgumentException(
                'No se pueden reservar horarios anteriores a la fecha actual.'
            );
        }

        $fechaMaxima = $hoy->modify(
            '+' . self::DIAS_MAXIMOS_ANTICIPACION . ' days'
        );

        if ($fechaObjeto > $fechaMaxima) {
            throw new InvalidArgumentException(
                sprintf(
                    'No se pueden realizar reservas con más de %d días de anticipación.',
                    self::DIAS_MAXIMOS_ANTICIPACION
                )
            );
        }

        /*
         * =========================================================
         * VALIDACIÓN DEL MOTIVO
         * =========================================================
         */

        $motivo = trim($motivo);

        if ($motivo === '') {
            throw new InvalidArgumentException(
                'Debe indicar el motivo de la reserva.'
            );
        }

        if (mb_strlen($motivo) > 255) {
            throw new InvalidArgumentException(
                'El motivo no puede superar los 255 caracteres.'
            );
        }

        /*
         * =========================================================
         * COMPROBAR DISPONIBILIDAD
         * =========================================================
         */

        if (
            $this->repository->existeReserva(
                $fecha,
                $idLaboratorio,
                $idHorario
            )
        ) {
            throw new InvalidArgumentException(
                'El bloque seleccionado ya se encuentra reservado.'
            );
        }

        /*
         * =========================================================
         * ESTADO INICIAL
         * =========================================================
         *
         * Ya no utilizamos:
         *
         * id_estado = 1
         *
         * Buscamos el estado "Pendiente" directamente en la BD.
         */

        $estadoPendiente = $this->estadoRepository->findByNombre(
            'Pendiente'
        );

        if ($estadoPendiente === null) {
            throw new RuntimeException(
                'No se encuentra configurado el estado Pendiente.'
            );
        }

        if (!(bool) $estadoPendiente['activo']) {
            throw new RuntimeException(
                'El estado Pendiente se encuentra inactivo.'
            );
        }

        $idEstado = (int) $estadoPendiente['id_estado'];

        if ($idEstado <= 0) {
            throw new RuntimeException(
                'El estado Pendiente no tiene un identificador válido.'
            );
        }

        /*
         * =========================================================
         * CREAR MODELO
         * =========================================================
         */

        $reserva = new Reserva(
            id_usuario: $idUsuario,
            id_curso: $idCurso,
            id_laboratorio: $idLaboratorio,
            id_horario: $idHorario,
            id_estado: $idEstado,
            fecha: $fecha,
            motivo: $motivo
        );

        /*
         * =========================================================
         * GUARDAR RESERVA
         * =========================================================
         */

        $idReserva = $this->repository->create($reserva);

        /*
         * =========================================================
         * TOKEN DE CONFIRMACIÓN Y CORREO AL DOCENTE
         * =========================================================
         *
         * Un fallo al enviar el correo no debe revertir ni afectar
         * la reserva ya guardada (NotificacionService registra el
         * error en storage/logs/mail.log en vez de lanzar excepción).
         */

        $token = $this->repository->generarToken($idReserva, $fecha);

        $this->enviarCorreoConfirmacion($idReserva, $token);
    }

    /**
     * Confirma una reserva a partir del enlace enviado por correo.
     */
    public function confirmarPorToken(int $id, string $token): void
    {
        $this->validarToken($id, $token);

        $this->cambiarEstadoPorNombre($id, 'Confirmada');
    }

    /**
     * Cancela una reserva a partir del enlace enviado por correo.
     */
    public function cancelarPorToken(int $id, string $token): void
    {
        $this->validarToken($id, $token);

        $this->cambiarEstadoPorNombre($id, 'Cancelada');
    }

    /**
     * Valida que el token recibido corresponda a la reserva indicada,
     * que no haya expirado y que la reserva siga Pendiente.
     */
    private function validarToken(int $id, string $token): array
    {
        date_default_timezone_set('America/Santiago');

        $token = trim($token);

        if ($id <= 0 || $token === '') {
            throw new InvalidArgumentException(
                'El enlace no es válido.'
            );
        }

        $fila = $this->repository->buscarToken($id);

        if (
            $fila === null ||
            $fila['token_confirmacion'] === null ||
            !hash_equals((string) $fila['token_confirmacion'], $token)
        ) {
            throw new InvalidArgumentException(
                'El enlace no es válido o ya fue utilizado.'
            );
        }

        if (
            $fila['token_expira'] === null ||
            new DateTimeImmutable((string) $fila['token_expira']) < new DateTimeImmutable('now')
        ) {
            throw new InvalidArgumentException(
                'El enlace ha expirado.'
            );
        }

        $estadoPendiente = $this->estadoRepository->findByNombre('Pendiente');

        if (
            $estadoPendiente === null ||
            (int) $fila['id_estado'] !== (int) $estadoPendiente['id_estado']
        ) {
            throw new InvalidArgumentException(
                'Esta reserva ya fue procesada anteriormente.'
            );
        }

        return $fila;
    }

    /**
     * Cambia el estado de la reserva por nombre e invalida su token,
     * para que el enlace del correo no pueda reutilizarse.
     */
    private function cambiarEstadoPorNombre(int $id, string $nombreEstado): void
    {
        $estado = $this->estadoRepository->findByNombre($nombreEstado);

        if ($estado === null) {
            throw new RuntimeException(
                "No se encuentra configurado el estado {$nombreEstado}."
            );
        }

        if (!(bool) $estado['activo']) {
            throw new RuntimeException(
                "El estado {$nombreEstado} se encuentra inactivo."
            );
        }

        $this->repository->actualizarEstado($id, (int) $estado['id_estado']);
        $this->repository->invalidarToken($id);
    }

    /**
     * Construye y envía el correo de confirmación/cancelación
     * al docente responsable de la reserva recién creada.
     */
    private function enviarCorreoConfirmacion(int $idReserva, string $token): void
    {
        $detalle = $this->repository->findDetalladaById($idReserva);

        if ($detalle === null || empty($detalle['correo'])) {
            return;
        }

        $baseUrl = rtrim((string) Config::get('APP_URL', ''), '/');

        $datosVista = [
            'nombres' => $detalle['nombres'],
            'apellidos' => $detalle['apellidos'],
            'fecha' => $detalle['fecha'],
            'horario' => $detalle['horario'],
            'hora_inicio' => $detalle['hora_inicio'],
            'hora_fin' => $detalle['hora_fin'],
            'laboratorio' => $detalle['laboratorio'],
            'curso' => $detalle['curso'],
            'motivo' => $detalle['motivo'],
            'urlConfirmar' => "{$baseUrl}/api/reservas/{$idReserva}/confirmar/{$token}",
            'urlCancelar' => "{$baseUrl}/api/reservas/{$idReserva}/cancelar/{$token}",
        ];

        $html = View::render('emails.reserva_creada_html', $datosVista, null);
        $texto = View::render('emails.reserva_creada_texto', $datosVista, null);

        $this->notificacion->enviarHtml(
            $detalle['correo'],
            'Confirma tu reserva de laboratorio - ReservaLab',
            $html,
            $texto
        );
    }
}