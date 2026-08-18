<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\HorarioService;
use Core\Controller;

final class HorarioController extends Controller
{
    private HorarioService $service;

    public function __construct()
    {
        $this->service = new HorarioService();
    }

    public function index(): string
    {
        return $this->view(
            'horarios.index',
            [
                'title' => 'Horarios',
                'horarios' => $this->service->listar(),
            ]
        );
    }
}