<?php

declare(strict_types=1);

namespace Core;

/**
 * Agrega un parámetro de versión (basado en la fecha de
 * modificación del archivo) a las rutas de CSS/JS estáticos, para
 * que el navegador detecte los cambios automáticamente en cada
 * despliegue en vez de servir una copia vieja desde su caché.
 */
final class Asset
{
    public static function url(string $rutaRelativa): string
    {
        $rutaAbsoluta = ($_SERVER['DOCUMENT_ROOT'] ?? '') . $rutaRelativa;

        $version = is_file($rutaAbsoluta) ? filemtime($rutaAbsoluta) : time();

        return $rutaRelativa . '?v=' . $version;
    }
}
