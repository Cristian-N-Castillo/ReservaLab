<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Observacion;
use App\Repositories\CursoRepository;
use App\Repositories\HorarioRepository;
use App\Repositories\LaboratorioRepository;
use App\Repositories\ObservacionRepository;
use App\Repositories\ReservaRepository;
use App\Repositories\UsuarioRepository;
use InvalidArgumentException;
use RuntimeException;

final class ObservacionService
{
    private const TAMANO_MAXIMO_PDF = 5 * 1024 * 1024; // 5 MB
    private const DIRECTORIO_UPLOADS = 'storage/uploads/observaciones';

    private ObservacionRepository $repository;
    private ReservaRepository $reservaRepository;
    private UsuarioRepository $usuarioRepository;
    private CursoRepository $cursoRepository;
    private LaboratorioRepository $laboratorioRepository;
    private HorarioRepository $horarioRepository;
    private NotificacionService $notificacion;

    public function __construct()
    {
        $this->repository = new ObservacionRepository();
        $this->reservaRepository = new ReservaRepository();
        $this->usuarioRepository = new UsuarioRepository();
        $this->cursoRepository = new CursoRepository();
        $this->laboratorioRepository = new LaboratorioRepository();
        $this->horarioRepository = new HorarioRepository();
        $this->notificacion = new NotificacionService();
    }

    /**
     * Obtiene todas las observaciones registradas en el sistema.
     */
    public function listar(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Obtiene las observaciones de una reserva específica.
     */
    public function listarPorReserva(int $idReserva): array
    {
        return $this->repository->findByReserva($idReserva);
    }

    /**
     * Registra una nueva observación sobre una reserva.
     *
     * Solo se debe invocar para usuarios con rol Administrador
     * (la restricción de acceso se aplica a nivel de ruta con
     * AdminMiddleware).
     *
     * Si existe un archivo PDF adjunto, se valida y se almacena.
     * Al finalizar, se envía (registra) una notificación por
     * correo al docente responsable de la reserva.
     */
    public function crear(
        int $idReserva,
        int $idUsuarioAdmin,
        string $texto,
        ?array $archivo
    ): void {

        $reserva = $this->reservaRepository->findById($idReserva);

        if (!$reserva) {
            throw new InvalidArgumentException(
                'La reserva indicada no existe.'
            );
        }

        $texto = trim($texto);

        if ($texto === '') {
            throw new InvalidArgumentException(
                'Debe ingresar el contenido de la observación.'
            );
        }

        if (mb_strlen($texto) > 2000) {
            throw new InvalidArgumentException(
                'La observación no puede superar los 2000 caracteres.'
            );
        }

        $nombreArchivo = null;

        if ($archivo !== null) {
            $nombreArchivo = $this->guardarPdf($archivo);
        }

        $observacion = new Observacion(
            id_reserva: $idReserva,
            id_usuario_admin: $idUsuarioAdmin,
            observacion: $texto,
            archivo_pdf: $nombreArchivo
        );

        $this->repository->create($observacion);

        $this->notificarDocente($reserva, $texto, $nombreArchivo);
    }

    /**
     * Busca una observación por su ID.
     */
    public function obtener(int $id): ?Observacion
    {
        return $this->repository->findById($id);
    }

    /**
     * Ruta absoluta en disco del PDF adjunto a una observación.
     */
    public function rutaPdf(Observacion $observacion): ?string
    {
        if ($observacion->archivo_pdf === null) {
            return null;
        }

        return dirname(__DIR__, 2) . '/' . self::DIRECTORIO_UPLOADS . '/' . $observacion->archivo_pdf;
    }

    /**
     * Valida y guarda el PDF adjunto, retornando el nombre
     * de archivo generado (no la ruta completa).
     */
    private function guardarPdf(array $archivo): string
    {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException(
                'No fue posible subir el archivo adjunto.'
            );
        }

        if ($archivo['size'] > self::TAMANO_MAXIMO_PDF) {
            throw new InvalidArgumentException(
                'El archivo PDF no puede superar los 5 MB.'
            );
        }

        $extension = strtolower(
            pathinfo($archivo['name'], PATHINFO_EXTENSION)
        );

        $mime = mime_content_type($archivo['tmp_name']);

        if ($extension !== 'pdf' || $mime !== 'application/pdf') {
            throw new InvalidArgumentException(
                'El archivo adjunto debe ser un PDF.'
            );
        }

        $directorio = dirname(__DIR__, 2) . '/' . self::DIRECTORIO_UPLOADS;

        if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
            throw new RuntimeException(
                'No fue posible preparar el directorio de almacenamiento.'
            );
        }

        $nombreArchivo = bin2hex(random_bytes(16)) . '.pdf';

        if (!move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombreArchivo)) {
            throw new RuntimeException(
                'No fue posible guardar el archivo adjunto.'
            );
        }

        return $nombreArchivo;
    }

    /**
     * Envía (registra) la notificación por correo al docente
     * responsable de la reserva observada.
     */
    private function notificarDocente(
        \App\Models\Reserva $reserva,
        string $texto,
        ?string $nombreArchivo
    ): void {

        $docente = $this->usuarioRepository->findById($reserva->id_usuario);

        if (!$docente || $docente->correo === '') {
            return;
        }

        $curso = $this->cursoRepository->findById($reserva->id_curso);
        $laboratorio = $this->laboratorioRepository->findById($reserva->id_laboratorio);
        $horario = $this->horarioRepository->findById($reserva->id_horario);

        $cuerpo = sprintf(
            "Estimado/a %s %s,\n\n" .
            "Se ha registrado una observación sobre su reserva del %s" .
            " (%s) en el laboratorio %s, curso %s.\n\n" .
            "Observación:\n%s\n\n%s",
            $docente->nombres,
            $docente->apellidos,
            $reserva->fecha,
            $horario?->nombre ?? "bloque #{$reserva->id_horario}",
            $laboratorio?->nombre ?? "laboratorio #{$reserva->id_laboratorio}",
            $curso?->nombre ?? "curso #{$reserva->id_curso}",
            $texto,
            $nombreArchivo !== null
                ? 'Se adjuntó un documento PDF con más detalles.'
                : ''
        );

        $this->notificacion->enviar(
            $docente->correo,
            'Nueva observación en su reserva - ReservaLab',
            trim($cuerpo)
        );
    }
}
