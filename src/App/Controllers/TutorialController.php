<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UsuarioService;
use Core\Controller;
use Core\Response;
use Core\Session;

final class TutorialController extends Controller
{
    private UsuarioService $service;

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    /**
     * Marca el tutorial guiado como visto, para que no se vuelva a
     * mostrar en próximos inicios de sesión. Se llama vía JS al
     * terminar o cerrar el recorrido.
     *
     * Termina con exit (como Response::redirect()): si no, el
     * valor de retorno nulo de este método hace que Application::run()
     * interprete que la ruta no encontró nada y envíe además una
     * respuesta 404 detrás del JSON ya enviado.
     */
    public function marcarVisto(): never
    {
        $idUsuario = (int) Session::get('usuario_id', 0);

        if ($idUsuario > 0) {

            $this->service->marcarTutorialVisto($idUsuario);

            Session::set('tutorial_visto', true);
        }

        (new Response())->json(['ok' => true]);

        exit;
    }
}
