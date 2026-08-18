<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Rol;
use App\Repositories\RolRepository;

final class RolService
{
    private RolRepository $repository;

    public function __construct()
    {
        $this->repository = new RolRepository();
    }

    /**
     * @return Rol[]
     */
    public function listar(): array
    {
        return $this->repository->findAll();
    }

    public function obtener(int $id): ?Rol
    {
        return $this->repository->findById($id);
    }
}