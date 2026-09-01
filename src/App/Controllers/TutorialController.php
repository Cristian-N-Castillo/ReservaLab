<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UsuarioService;
use Core\Controller;
use Core\Request;
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
     * Marca el tutorial guiado de un módulo como visto, para que no
     * se le vuelva a mostrar a este usuario. Se llama vía JS al
     * terminar o cerrar el recorrido de ese módulo.
     *
     * Termina con exit (como Response::redirect()): si no, el
     * valor de retorno nulo de este método hace que Application::run()
     * interprete que la ruta no encontró nada y envíe además una
     * respuesta 404 detrás del JSON ya enviado.
     */
    public function marcarVisto(Request $request): never
    {
        $idUsuario = (int) Session::get('usuario_id', 0);
        $modulo = trim((string) $request->input('modulo', ''));

        if ($idUsuario > 0 && $modulo !== '') {

            $this->service->marcarTutorialVisto($idUsuario, $modulo);

            $vistos = Session::get('tutoriales_vistos', []);

            if (!in_array($modulo, $vistos, true)) {
                $vistos[] = $modulo;
                Session::set('tutoriales_vistos', $vistos);
            }
        }

        (new Response())->json(['ok' => true]);

        exit;
    }
}
