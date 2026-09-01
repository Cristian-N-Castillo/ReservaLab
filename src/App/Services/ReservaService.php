<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Reserva;
use App\Repositories\CursoRepository;
use App\Repositories\EstadoReservaRepository;
use App\Repositories\HorarioRepository;
use App\Repositories\LaboratorioRepository;
use App\Repositories\ReservaRepository;
use App\Repositories\UsuarioRepository;
use Core\Config;
use Core\Database;
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

    /**
     * Máximo de bloques horarios que se pueden reservar juntos
     * en una sola operación (ver crearMultiple()).
     */
    private const MAX_BLOQUES_POR_RESERVA = 3;

    private ReservaRepository $repository;
    private EstadoReservaRepository $estadoRepository;
    private CursoRepository $cursoRepository;
    private LaboratorioRepository $laboratorioRepository;
    private HorarioRepository $horarioRepository;
    private UsuarioRepository $usuarioRepository;
    private NotificacionService $notificacion;

    public function __construct()
    {
        $this->repository = new ReservaRepository();
        $this->estadoRepository = new EstadoReservaRepository();
        $this->cursoRepository = new CursoRepository();
        $this->laboratorioRepository = new LaboratorioRepository();
        $this->horarioRepository = new HorarioRepository();
        $this->usuarioRepository = new UsuarioRepository();
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
     * Historial de reservas de un docente. $desde/$hasta son
     * opcionales: si se omiten, trae el historial completo.
     */
    public function historialPorUsuario(
        int $idUsuario,
        ?string $desde = null,
        ?string $hasta = null
    ): array {
        return $this->repository->historialPorUsuario($idUsuario, $desde, $hasta);
    }

    /**
     * Reservas Pendientes y Confirmadas dentro de un rango de
     * fechas, para pintar el calendario mensual. Las canceladas
     * (y cualquier otro estado) no se muestran ahí.
     */
    public function porRangoFechas(string $desde, string $hasta): array
    {
        $reservas = $this->repository->historial($desde, $hasta);

        return array_values(array_filter(
            $reservas,
            static fn (array $r): bool => in_array(
                mb_strtolower((string) $r['estado']),
                ['pendiente', 'confirmada'],
                true
            )
        ));
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

        $fecha = trim($fecha);

        $idReserva = $this->crearSinNotificar(
            $idUsuario,
            $idCurso,
            $idLaboratorio,
            $idHorario,
            $fecha,
            $motivo
        );

        $this->notificarReservaCreada($idReserva, $fecha);
    }

    /**
     * Crea hasta self::MAX_BLOQUES_POR_RESERVA reservas (una por cada
     * bloque horario indicado) en una sola operación, para que el
     * docente no tenga que repetir el formulario bloque por bloque.
     *
     * Todo o nada: si algún bloque no es válido o ya no está
     * disponible, no se crea ninguna reserva.
     */
    public function crearMultiple(
        int $idUsuario,
        int $idCurso,
        int $idLaboratorio,
        array $idsHorario,
        string $fecha,
        string $motivo
    ): void {

        $fecha = trim($fecha);

        $idsHorario = array_values(array_unique(array_map(
            'intval',
            $idsHorario
        )));

        if (empty($idsHorario)) {
            throw new InvalidArgumentException(
                'Debe seleccionar al menos un bloque horario.'
            );
        }

        if (count($idsHorario) > self::MAX_BLOQUES_POR_RESERVA) {
            throw new InvalidArgumentException(
                sprintf(
                    'No se pueden reservar más de %d bloques a la vez.',
                    self::MAX_BLOQUES_POR_RESERVA
                )
            );
        }

        $conexion = Database::connection();

        $conexion->beginTransaction();

        try {

            $idsReserva = [];

            foreach ($idsHorario as $idHorario) {

                $idsReserva[] = $this->crearSinNotificar(
                    $idUsuario,
                    $idCurso,
                    $idLaboratorio,
                    $idHorario,
                    $fecha,
                    $motivo
                );
            }

            $conexion->commit();

        } catch (\Throwable $excepcion) {

            $conexion->rollBack();

            throw $excepcion;
        }

        /*
         * El correo se envía recién después del commit: un fallo al
         * enviar no debe revertir reservas ya guardadas.
         *
         * Si se reservó un solo bloque, se usa el correo individual
         * de siempre. Si se reservaron varios, se envía un único
         * correo con todos los bloques juntos (uno por reserva sería
         * tedioso para el docente).
         */
        if (count($idsReserva) === 1) {
            $this->notificarReservaCreada($idsReserva[0], $fecha);
        } else {
            $this->notificarReservasCreadas($idsReserva, $fecha);
        }
    }

    /**
     * Valida y guarda una reserva, sin enviar el correo de
     * confirmación. Utilizado tanto por crear() como por
     * crearMultiple() para reutilizar exactamente las mismas
     * validaciones.
     */
    private function crearSinNotificar(
        int $idUsuario,
        int $idCurso,
        int $idLaboratorio,
        int $idHorario,
        string $fecha,
        string $motivo
    ): int {

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

        date_default_timezone_set('America/Santiago');

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

        $diaSemana = (int) $fechaObjeto->format('N');

        if ($diaSemana >= 6) {
            throw new InvalidArgumentException(
                'No se pueden realizar reservas los días sábado ni domingo.'
            );
        }

        /*
         * Si la reserva es para el día de hoy, el bloque horario
         * elegido ya no puede haber terminado.
         */
        if ($fechaObjeto == $hoy) {

            $horaActual = (new \DateTimeImmutable('now'))->format('H:i:s');

            if ($horario->hora_fin !== '' && $horaActual >= $horario->hora_fin) {
                throw new InvalidArgumentException(
                    sprintf(
                        'No se puede reservar el bloque "%s": ya pasó su horario de término (%s).',
                        $horario->nombre,
                        substr($horario->hora_fin, 0, 5)
                    )
                );
            }
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

        return $this->repository->create($reserva);
    }

    /**
     * Genera el token de confirmación y envía el correo al docente
     * para una reserva ya guardada.
     *
     * Un fallo al enviar el correo no debe revertir ni afectar la
     * reserva ya guardada (NotificacionService registra el error en
     * storage/logs/mail.log en vez de lanzar excepción).
     */
    private function notificarReservaCreada(int $idReserva, string $fecha): void
    {
        $token = $this->repository->generarToken($idReserva, $fecha);

        $this->enviarCorreoConfirmacion($idReserva, $token);
    }

    /**
     * Genera el token de cada reserva y envía un único correo con
     * todos los bloques juntos (usado cuando crearMultiple() creó
     * más de una reserva). Cada bloque conserva su propio enlace de
     * confirmar/cancelar, ya que sigue siendo una reserva
     * independiente.
     *
     * Un fallo al enviar el correo no debe afectar las reservas ya
     * guardadas.
     */
    private function notificarReservasCreadas(array $idsReserva, string $fecha): void
    {
        $baseUrl = rtrim((string) Config::get('APP_URL', ''), '/');

        $bloques = [];
        $detalleComun = null;

        foreach ($idsReserva as $idReserva) {

            $detalle = $this->repository->findDetalladaById($idReserva);

            if ($detalle === null || empty($detalle['correo'])) {
                continue;
            }

            $detalleComun ??= $detalle;

            $token = $this->repository->generarToken($idReserva, $fecha);

            $bloques[] = [
                'horario' => $detalle['horario'],
                'hora_inicio' => $detalle['hora_inicio'],
                'hora_fin' => $detalle['hora_fin'],
                'urlConfirmar' => "{$baseUrl}/api/reservas/{$idReserva}/confirmar/{$token}",
                'urlCancelar' => "{$baseUrl}/api/reservas/{$idReserva}/cancelar/{$token}",
            ];
        }

        if ($detalleComun === null || empty($bloques)) {
            return;
        }

        $datosVista = [
            'nombres' => $detalleComun['nombres'],
            'apellidos' => $detalleComun['apellidos'],
            'fecha' => $detalleComun['fecha'],
            'laboratorio' => $detalleComun['laboratorio'],
            'curso' => $detalleComun['curso'],
            'motivo' => $detalleComun['motivo'],
            'bloques' => $bloques,
        ];

        $html = View::render('emails.reserva_creada_multiple_html', $datosVista, null);
        $texto = View::render('emails.reserva_creada_multiple_texto', $datosVista, null);

        $this->notificacion->enviarHtml(
            $detalleComun['correo'],
            'Confirma tus reservas de laboratorio - ReservaLab',
            $html,
            $texto
        );
    }

    /**
     * Confirma una reserva a partir del enlace enviado por correo.
     */
    public function confirmarPorToken(int $id, string $token): void
    {
        $this->validarToken($id, $token);

        $this->cambiarEstadoPorNombre($id, 'Confirmada');

        $this->enviarCorreoConfirmacionAdmins($id);
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
     * Marca como Finalizada toda reserva Pendiente o Confirmada
     * cuyo bloque horario ya haya terminado.
     *
     * Pensado para ejecutarse periódicamente desde un cron
     * (ver console reservas:finalizar).
     *
     * Retorna la cantidad de reservas finalizadas.
     */
    public function finalizarVencidas(): int
    {
        date_default_timezone_set('America/Santiago');

        $estadoFinalizada = $this->estadoRepository->findByNombre('Finalizada');

        if ($estadoFinalizada === null) {
            throw new RuntimeException(
                'No se encuentra configurado el estado Finalizada.'
            );
        }

        if (!(bool) $estadoFinalizada['activo']) {
            throw new RuntimeException(
                'El estado Finalizada se encuentra inactivo.'
            );
        }

        $hoy = (new DateTimeImmutable('today'))->format('Y-m-d');
        $horaActual = (new DateTimeImmutable('now'))->format('H:i:s');

        return $this->repository->finalizarVencidas(
            (int) $estadoFinalizada['id_estado'],
            $hoy,
            $horaActual
        );
    }

    /**
     * Envía un correo de recordatorio a las reservas que llevan más
     * de $minutos minutos en estado Pendiente, sin confirmar ni
     * cancelar. Cada reserva recibe el recordatorio una sola vez.
     *
     * Pensado para ejecutarse periódicamente desde un cron
     * (ver console reservas:recordatorios).
     *
     * Retorna la cantidad de recordatorios enviados.
     */
    public function enviarRecordatoriosPendientes(int $minutos = 5): int
    {
        $pendientes = $this->repository->pendientesParaRecordatorio($minutos);

        $baseUrl = rtrim((string) Config::get('APP_URL', ''), '/');

        $enviados = 0;

        foreach ($pendientes as $reserva) {

            if (empty($reserva['correo'])) {
                continue;
            }

            $idReserva = (int) $reserva['id_reserva'];
            $token = (string) $reserva['token_confirmacion'];

            $datosVista = [
                'nombres' => $reserva['nombres'],
                'apellidos' => $reserva['apellidos'],
                'fecha' => $reserva['fecha'],
                'horario' => $reserva['horario'],
                'hora_inicio' => $reserva['hora_inicio'],
                'hora_fin' => $reserva['hora_fin'],
                'laboratorio' => $reserva['laboratorio'],
                'curso' => $reserva['curso'],
                'urlConfirmar' => "{$baseUrl}/api/reservas/{$idReserva}/confirmar/{$token}",
                'urlCancelar' => "{$baseUrl}/api/reservas/{$idReserva}/cancelar/{$token}",
            ];

            $html = View::render('emails.reserva_recordatorio_html', $datosVista, null);
            $texto = View::render('emails.reserva_recordatorio_texto', $datosVista, null);

            $this->notificacion->enviarHtml(
                $reserva['correo'],
                'Recordatorio: confirma tu reserva de laboratorio - ReservaLab',
                $html,
                $texto
            );

            $this->repository->marcarRecordatorioEnviado($idReserva);

            $enviados++;
        }

        return $enviados;
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

    /**
     * Avisa a todos los administradores activos que un docente
     * confirmó una reserva. Un fallo al enviar el correo no debe
     * afectar la confirmación ya guardada (NotificacionService
     * registra el error en storage/logs/mail.log en vez de lanzar
     * excepción).
     */
    private function enviarCorreoConfirmacionAdmins(int $idReserva): void
    {
        $correos = $this->usuarioRepository->findCorreosAdministradores();

        if (empty($correos)) {
            return;
        }

        $detalle = $this->repository->findDetalladaById($idReserva);

        if ($detalle === null) {
            return;
        }

        $datosVista = [
            'nombres' => $detalle['nombres'],
            'apellidos' => $detalle['apellidos'],
            'fecha' => $detalle['fecha'],
            'horario' => $detalle['horario'],
            'hora_inicio' => $detalle['hora_inicio'],
            'hora_fin' => $detalle['hora_fin'],
            'laboratorio' => $detalle['laboratorio'],
            'curso' => $detalle['curso'],
        ];

        $html = View::render('emails.reserva_confirmada_html', $datosVista, null);
        $texto = View::render('emails.reserva_confirmada_texto', $datosVista, null);

        foreach ($correos as $correo) {

            $this->notificacion->enviarHtml(
                $correo,
                'Reserva confirmada por un docente - ReservaLab',
                $html,
                $texto
            );
        }
    }
}