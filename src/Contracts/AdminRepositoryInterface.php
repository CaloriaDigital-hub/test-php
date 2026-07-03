<?php
declare(strict_types=1);

namespace App\Contracts;

use App\Models\AuthUser;

interface AdminRepositoryInterface
{
    public function findByUsername(string $username): ?AuthUser;
}