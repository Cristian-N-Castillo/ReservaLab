<?php

declare(strict_types=1);

namespace Core;

use InvalidArgumentException;
use RuntimeException;

abstract class Controller
{
    protected function view(
        string $view,
        array $data = [],
        ?string $layout = 'app'
    ): string {
        return View::render(
            $view,
            $data,
            $layout
        );
    }

    /**
     * Ejecuta una acción de escritura (crear/actualizar/eliminar),
     * deja un mensaje Flash de éxito o error según corresponda, y
     * redirige. Cubre el patrón try/catch/flash/redirect repetido
     * en la mayoría de los controladores.
     *
     * Si $redirectError se omite, se usa el mismo destino del éxito
     * (caso típico de destroy()/toggle(), donde no hay una pantalla
     * de formulario a la que volver tras un error).
     */
    protected function ejecutarConFlash(
        callable $accion,
        string $mensajeExito,
        string $redirectExito,
        ?string $redirectError = null
    ): never {
        $response = new Response();

        try {

            $accion();

            Session::flash('success', $mensajeExito);

            $response->redirect($redirectExito);

        } catch (InvalidArgumentException|RuntimeException $e) {

            Session::flash('error', $e->getMessage());

            $response->redirect($redirectError ?? $redirectExito);
        }
    }
}