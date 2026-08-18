<?php

declare(strict_types=1);

namespace Core;

final class Application
{
    private Request $request;
    private Response $response;
    private Router $router;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router();

        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        $router = $this->router;

        require dirname(__DIR__) . '/routes/web.php';
        require dirname(__DIR__) . '/routes/usuarios.php';
        require dirname(__DIR__) . '/routes/auth.php';
        require dirname(__DIR__) . '/routes/admin.php';
        require dirname(__DIR__) . '/routes/cursos.php';
        require dirname(__DIR__) . '/routes/horarios.php';
        require dirname(__DIR__) . '/routes/laboratorios.php';
        require dirname(__DIR__) . '/routes/reservas.php';
        require dirname(__DIR__) . '/routes/observaciones.php';
        require dirname(__DIR__) . '/routes/reportes.php';
        require dirname(__DIR__) . '/routes/api.php';
        
    }

    public function run(): void
    {
        try {

            $result = $this->router->dispatch($this->request);

            if ($result === null) {

                $this->response
                    ->status(404)
                    ->html('<h1>404 - Página no encontrada</h1>');

                return;
            }

            if (is_string($result)) {
                $this->response->html($result);
            }

        } catch (\Throwable $exception) {

            (new ExceptionHandler())->render($exception);

        }
    }
}