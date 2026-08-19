<?php

declare(strict_types=1);

namespace Core;

/**
 * Validadores genéricos de formato, reutilizables entre servicios.
 */
final class Validator
{
    /**
     * Valida un RUT chileno (con dígito verificador, módulo 11).
     *
     * Acepta formatos como "12345678-9" o "12.345.678-9".
     * No valida existencia real de la persona, solo el formato
     * y que el dígito verificador sea consistente.
     */
    public static function rutValido(string $rut): bool
    {
        $rut = strtoupper(str_replace(['.', ' '], '', trim($rut)));

        if (!preg_match('/^(\d{1,8})-([0-9K])$/', $rut, $coincidencias)) {
            return false;
        }

        [, $numero, $dv] = $coincidencias;

        $suma = 0;
        $multiplo = 2;

        foreach (array_reverse(str_split($numero)) as $digito) {

            $suma += ((int) $digito) * $multiplo;

            $multiplo = $multiplo === 7 ? 2 : $multiplo + 1;
        }

        $resto = 11 - ($suma % 11);

        $dvCalculado = match ($resto) {
            11 => '0',
            10 => 'K',
            default => (string) $resto,
        };

        return $dvCalculado === $dv;
    }

    /**
     * Valida el formato de un correo electrónico.
     */
    public static function emailValido(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
