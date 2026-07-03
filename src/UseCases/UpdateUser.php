<?php
declare(strict_types=1);

namespace App\UseCases;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Validators\UpdateUserValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class UpdateUser
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private UpdateUserValidator $validator
    ) {}

    public function execute(int $id, array $input): void
    {
        // Validate first, before any DB queries
        $errors = $this->validator->validate($input);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        // Need the existing record to preserve the password hash if not changing it
        $existing = $this->repository->findById($id);
        if (!$existing) {
            throw new NotFoundException('User', $id);
        }

        // Check uniqueness but exclude the current user — otherwise their own login would fail
        if ($this->repository->loginExists($input['login'], $id)) {
            throw new ValidationException(['login' => 'Login already taken.']);
        }

        // If password field is empty, keep the old hash — admin doesn't have to reset it every time
        $passwordHash = !empty($input['password'])
            ? password_hash($input['password'], PASSWORD_DEFAULT)
            : $existing->passwordHash;

        $updatedUser = new User(
            id: $id,
            login: $input['login'],
            passwordHash: $passwordHash,
            firstName: $input['first_name'],
            lastName: $input['last_name'],
            gender: $input['gender'],
            birthDate: $input['birth_date'],
            createdAt: $existing->createdAt,  // preserve original timestamps
            updatedAt: $existing->updatedAt
        );

        $this->repository->update($id, $updatedUser);
    }
}