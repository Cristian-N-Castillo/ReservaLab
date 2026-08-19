<?php

declare(strict_types=1);

namespace Core;

/**
 * Genera y valida el token CSRF de la sesión actual.
 */
final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Obtiene el token de la sesión actual, generándolo si no existe.
     */
    public static function token(): string
    {
        if (!Session::has(self::SESSION_KEY)) {
            Session::set(self::SESSION_KEY, bin2hex(random_bytes(32)));
        }

        return (string) Session::get(self::SESSION_KEY);
    }

    /**
     * Campo oculto listo para incrustar en un formulario POST.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="'
            . htmlspecialchars(self::token())
            . '">';
    }

    /**
     * Verifica que el token recibido coincida con el de la sesión.
     */
    public static function verify(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        return hash_equals(self::token(), $token);
    }
}
