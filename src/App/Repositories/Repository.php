<?php

declare(strict_types=1);

namespace App\Repositories;

use Core\Database;
use PDO;

abstract class Repository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }
}