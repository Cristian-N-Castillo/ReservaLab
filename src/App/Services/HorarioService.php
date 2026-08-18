<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\HorarioRepository;

final class HorarioService
{
    private HorarioRepository $repository;

    public function __construct()
    {
        $this->repository = new HorarioRepository();
    }

    public function listar(): array
    {
        return $this->repository->findAll();
    }
}