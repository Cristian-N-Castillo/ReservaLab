<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UsuarioRepository;
use Core\Session;
use RuntimeException;

final class AuthService
{
    private const MAX_INTENTOS = 5;
    private const BLOQUEO_MINUTOS = 5;

    private UsuarioRepository $repository;

    public function __construct()
    {
        $this->repository = new UsuarioRepository();
    }

    /**
     * Intenta iniciar sesión.
     *
     * Lanza RuntimeException cuando la cuenta está temporalmente
     * bloqueada por demasiados intentos fallidos. Retorna false
     * para credenciales inválidas (sin distinguir el motivo, para
     * no filtrar si el RUT existe o no).
     */
    public function login(string $rut, string $password): bool
    {
        $this->verificarBloqueo();

        $usuario = $this->repository->findByRut($rut);

        $credencialesValidas =
            $usuario !== null &&
            $usuario->activo &&
            password_verify($password, $usuario->password);

        if (!$credencialesValidas) {
            $this->registrarIntentoFallido();

            return false;
        }

        $this->limpiarIntentosFallidos();

        Session::regenerate();

        Session::set('usuario_id', $usuario->id_usuario);

        Session::set(
            'nombre',
            trim($usuario->nombres . ' ' . $usuario->apellidos)
        );

        Session::set(
            'rol',
            $usuario->rol_nombre
        );

        Session::set(
            'id_rol',
            $usuario->id_rol
        );

        Session::set(
            'debe_cambiar_password',
            $usuario->debe_cambiar_password
        );

        return true;
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function check(): bool
    {
        return Session::has('usuario_id');
    }

    private function verificarBloqueo(): void
    {
        $bloqueadoHasta = Session::get('_login_bloqueado_hasta');

        if ($bloqueadoHasta === null || time() >= (int) $bloqueadoHasta) {
            return;
        }

        $minutosRestantes = (int) ceil(
            ((int) $bloqueadoHasta - time()) / 60
        );

        throw new RuntimeException(
            sprintf(
                'Demasiados intentos fallidos. Intente nuevamente en %d minuto(s).',
                max(1, $minutosRestantes)
            )
        );
    }

    private function registrarIntentoFallido(): void
    {
        $intentos = (int) Session::get('_login_intentos', 0) + 1;

        Session::set('_login_intentos', $intentos);

        if ($intentos >= self::MAX_INTENTOS) {

            Session::set(
                '_login_bloqueado_hasta',
                time() + (self::BLOQUEO_MINUTOS * 60)
            );

            Session::set('_login_intentos', 0);
        }
    }

    private function limpiarIntentosFallidos(): void
    {
        Session::remove('_login_intentos');
        Session::remove('_login_bloqueado_hasta');
    }
}
