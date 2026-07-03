<?php
declare(strict_types=1);

namespace App\UseCases;

use App\Contracts\UserRepositoryInterface;
use RuntimeException;

class DeleteUser
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        $user = $this->repository->findById($id);
        if (!$user) {
            throw new RuntimeException('User not found');
        }
        $this->repository->delete($id);
    }
}