<?php
declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;
use App\Models\UserListItem;

interface UserRepositoryInterface
{
    /** @return UserListItem[] */
    public function getPaginatedList(int $page, string $sort, string $dir, int $perPage): array;

    public function countAll(): int;

    /**
     * Returns a read-only projection (UserListItem) for display in list/detail views.
     * Does NOT include the password hash — use this for any output to the user.
     */
    public function findByIdForDisplay(int $id): ?UserListItem;

    /**
     * Returns the full User entity including all fields.
     * Use this when you need to populate an edit form or pass data into a write UseCase.
     */
    public function findById(int $id): ?User;

    public function loginExists(string $login, ?int $excludeId = null): bool;

    public function create(User $user): int;

    public function update(int $id, User $user): bool;

    public function delete(int $id): bool;
}