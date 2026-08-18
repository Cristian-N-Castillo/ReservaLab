<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\RolRepository;
use App\Repositories\UsuarioRepository;
use Core\View;
use Core\Validator;
use InvalidArgumentException;
use RuntimeException;

final class UsuarioService
{
    private const PASSWORD_MINIMO = 8;
    private const RESET_INTENTOS_MAXIMO = 5;

    private UsuarioRepository $repository;
    private RolRepository $rolRepository;
    private NotificacionService $notificacion;

    public function __construct()
    {
        $this->repository = new UsuarioRepository();
        $this->rolRepository = new RolRepository();
        $this->notificacion = new NotificacionService();
    }

    /**
     * @return Usuario[]
     */
    public function listar(): array
    {
        return $this->repository->findAll();
    }

    public function obtener(int $id): ?Usuario
    {
        return $this->repository->findById($id);
    }

    public function obtenerPorRut(string $rut): ?Usuario
    {
        return $this->repository->findByRut($rut);
    }

    public function obtenerPorCorreo(string $correo): ?Usuario
    {
        return $this->repository->findByCorreo($correo);
    }

    /**
     * Busca un usuario por RUT o correo, según el formato del valor
     * recibido (se usa en la recuperación de contraseña, donde el
     * usuario puede ingresar cualquiera de los dos).
     */
    public function obtenerPorRutOCorreo(string $valor): ?Usuario
    {
        $valor = trim($valor);

        if ($valor === '') {
            return null;
        }

        return str_contains($valor, '@')
            ? $this->repository->findByCorreo($valor)
            : $this->repository->findByRut($valor);
    }

    public function cantidadUsuarios(): int
    {
        return $this->repository->count();
    }

    public function crear(Usuario $usuario): void
    {
        $this->validarDatosBasicos($usuario);

        if (mb_strlen($usuario->password) < self::PASSWORD_MINIMO) {
            throw new InvalidArgumentException(
                sprintf(
                    'La contraseña debe tener al menos %d caracteres.',
                    self::PASSWORD_MINIMO
                )
            );
        }

        if ($this->repository->findByRut($usuario->rut)) {
            throw new RuntimeException(
                'El RUT ya se encuentra registrado.'
            );
        }

        if ($this->repository->findByCorreo($usuario->correo)) {
            throw new RuntimeException(
                'El correo ya se encuentra registrado.'
            );
        }

        $usuario->password = password_hash(
            $usuario->password,
            PASSWORD_DEFAULT
        );

        $this->repository->create($usuario);
    }

    public function actualizar(Usuario $usuario): void
    {
        $this->validarDatosBasicos($usuario);

        $usuarioActual = $this->repository->findById($usuario->id_usuario);

        if (!$usuarioActual) {
            throw new RuntimeException('El usuario no existe.');
        }

        $rut = $this->repository->findByRut($usuario->rut);

        if (
            $rut &&
            $rut->id_usuario !== $usuario->id_usuario
        ) {
            throw new RuntimeException(
                'El RUT ya se encuentra registrado.'
            );
        }

        $correo = $this->repository->findByCorreo($usuario->correo);

        if (
            $correo &&
            $correo->id_usuario !== $usuario->id_usuario
        ) {
            throw new RuntimeException(
                'El correo ya se encuentra registrado.'
            );
        }

        $this->repository->update($usuario);
    }

    public function cambiarEstado(int $id, bool $activo): void
    {
        $this->repository->changeStatus($id, $activo);
    }

    /**
     * Cambia la contraseña de un usuario (usado en el primer inicio
     * de sesión, cuando debe_cambiar_password está activo). No pide
     * la contraseña actual porque el usuario ya se autenticó con ella
     * momentos antes, dentro de la misma sesión.
     */
    public function cambiarPassword(int $id, string $nueva, string $confirmacion): void
    {
        if ($nueva === '' || $confirmacion === '') {
            throw new InvalidArgumentException(
                'Debe ingresar y confirmar la nueva contraseña.'
            );
        }

        if ($nueva !== $confirmacion) {
            throw new InvalidArgumentException(
                'Las contraseñas no coinciden.'
            );
        }

        if (mb_strlen($nueva) < self::PASSWORD_MINIMO) {
            throw new InvalidArgumentException(
                sprintf(
                    'La contraseña debe tener al menos %d caracteres.',
                    self::PASSWORD_MINIMO
                )
            );
        }

        $usuario = $this->repository->findById($id);

        if (!$usuario) {
            throw new RuntimeException(
                'El usuario no existe.'
            );
        }

        $hash = password_hash($nueva, PASSWORD_DEFAULT);

        $this->repository->actualizarPassword($id, $hash);
    }

    /**
     * Solicita un código de recuperación de contraseña y lo envía
     * por correo. No lanza excepción ni revela si el RUT/correo
     * existe: si no hay coincidencia, simplemente no hace nada, para
     * no filtrar qué cuentas están registradas.
     */
    public function solicitarRecuperacion(string $identificador): void
    {
        $usuario = $this->obtenerPorRutOCorreo($identificador);

        if (!$usuario || !$usuario->activo) {
            return;
        }

        $codigo = $this->repository->generarCodigoReset($usuario->id_usuario);

        $datosVista = [
            'nombres' => $usuario->nombres,
            'apellidos' => $usuario->apellidos,
            'codigo' => $codigo,
        ];

        $html = View::render('emails.reset_password_html', $datosVista, null);
        $texto = View::render('emails.reset_password_texto', $datosVista, null);

        $this->notificacion->enviarHtml(
            $usuario->correo,
            'Código para recuperar tu contraseña - ReservaLab',
            $html,
            $texto
        );
    }

    /**
     * Valida el código de recuperación enviado por correo y, si es
     * correcto y no ha expirado, define la nueva contraseña.
     */
    public function restablecerPasswordConCodigo(
        string $identificador,
        string $codigo,
        string $nueva,
        string $confirmacion
    ): void {
        $usuario = $this->obtenerPorRutOCorreo($identificador);

        if (!$usuario) {
            throw new InvalidArgumentException(
                'El código ingresado no es válido o ha expirado.'
            );
        }

        $datos = $this->repository->buscarCodigoReset($usuario->id_usuario);

        if (!$datos || $datos['reset_codigo'] === null) {
            throw new InvalidArgumentException(
                'Debe solicitar un nuevo código de recuperación.'
            );
        }

        if ((int) $datos['reset_intentos'] >= self::RESET_INTENTOS_MAXIMO) {
            $this->repository->limpiarCodigoReset($usuario->id_usuario);

            throw new InvalidArgumentException(
                'Se superó el número de intentos permitidos. Solicite un nuevo código.'
            );
        }

        if (strtotime((string) $datos['reset_codigo_expira']) < time()) {
            $this->repository->limpiarCodigoReset($usuario->id_usuario);

            throw new InvalidArgumentException(
                'El código ha expirado. Solicite uno nuevo.'
            );
        }

        if (!hash_equals((string) $datos['reset_codigo'], hash('sha256', trim($codigo)))) {
            $this->repository->incrementarIntentosReset($usuario->id_usuario);

            throw new InvalidArgumentException(
                'El código ingresado no es correcto.'
            );
        }

        if ($nueva === '' || $confirmacion === '') {
            throw new InvalidArgumentException(
                'Debe ingresar y confirmar la nueva contraseña.'
            );
        }

        if ($nueva !== $confirmacion) {
            throw new InvalidArgumentException(
                'Las contraseñas no coinciden.'
            );
        }

        if (mb_strlen($nueva) < self::PASSWORD_MINIMO) {
            throw new InvalidArgumentException(
                sprintf(
                    'La contraseña debe tener al menos %d caracteres.',
                    self::PASSWORD_MINIMO
                )
            );
        }

        $hash = password_hash($nueva, PASSWORD_DEFAULT);

        $this->repository->actualizarPassword($usuario->id_usuario, $hash);
        $this->repository->limpiarCodigoReset($usuario->id_usuario);
    }

    public function eliminar(int $id): void
    {
        $usuario = $this->repository->findById($id);

        if (!$usuario) {
            throw new RuntimeException(
                'El usuario no existe.'
            );
        }

        $this->repository->delete($id);
    }

    /**
     * Valida formato y largo de los campos básicos de un usuario.
     * No valida la contraseña (solo aplica al crear).
     */
    private function validarDatosBasicos(Usuario $usuario): void
    {
        $usuario->rut = trim($usuario->rut);

        if ($usuario->rut === '') {
            throw new InvalidArgumentException(
                'El RUT es obligatorio.'
            );
        }

        if (!Validator::rutValido($usuario->rut)) {
            throw new InvalidArgumentException(
                'El RUT ingresado no es válido.'
            );
        }

        if (mb_strlen($usuario->rut) > 12) {
            throw new InvalidArgumentException(
                'El RUT no puede superar los 12 caracteres.'
            );
        }

        $usuario->nombres = trim($usuario->nombres);

        if ($usuario->nombres === '') {
            throw new InvalidArgumentException(
                'Los nombres son obligatorios.'
            );
        }

        if (mb_strlen($usuario->nombres) > 100) {
            throw new InvalidArgumentException(
                'Los nombres no pueden superar los 100 caracteres.'
            );
        }

        $usuario->apellidos = trim($usuario->apellidos);

        if ($usuario->apellidos === '') {
            throw new InvalidArgumentException(
                'Los apellidos son obligatorios.'
            );
        }

        if (mb_strlen($usuario->apellidos) > 100) {
            throw new InvalidArgumentException(
                'Los apellidos no pueden superar los 100 caracteres.'
            );
        }

        $usuario->correo = trim($usuario->correo);

        if ($usuario->correo === '') {
            throw new InvalidArgumentException(
                'El correo es obligatorio.'
            );
        }

        if (!Validator::emailValido($usuario->correo)) {
            throw new InvalidArgumentException(
                'El correo ingresado no es válido.'
            );
        }

        if (mb_strlen($usuario->correo) > 150) {
            throw new InvalidArgumentException(
                'El correo no puede superar los 150 caracteres.'
            );
        }

        $usuario->telefono = trim($usuario->telefono);

        if (mb_strlen($usuario->telefono) > 20) {
            throw new InvalidArgumentException(
                'El teléfono no puede superar los 20 caracteres.'
            );
        }

        if (!$this->rolRepository->findById($usuario->id_rol)) {
            throw new InvalidArgumentException(
                'El rol seleccionado no es válido.'
            );
        }
    }
}
