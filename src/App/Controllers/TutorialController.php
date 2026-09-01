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
     */
    public function marcarVisto(): void
    {
        $idUsuario = (int) Session::get('usuario_id', 0);

        if ($idUsuario > 0) {

            $this->service->marcarTutorialVisto($idUsuario);

            Session::set('tutorial_visto', true);
        }

        (new Response())->json(['ok' => true]);
    }
}
