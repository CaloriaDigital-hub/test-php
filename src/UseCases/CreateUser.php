<?php
declare(strict_types=1);

namespace App\UseCases;

use App\Contracts\UserRepositoryInterface;
use App\Exceptions\ValidationException;
use App\Models\User;
use App\Validators\CreateUserValidator;

class CreateUser
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private CreateUserValidator $validator
    ) {}

    // Returns the new user's ID
    public function execute(array $input): int
    {
        $errors = $this->validator->validate($input);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        // Login uniqueness check before we even try to INSERT
        if ($this->repository->loginExists($input['login'])) {
            throw new ValidationException(['login' => 'Login already taken.']);
        }

        // id=0 because auto-increment will assign the real one
        $user = new User(
            id: 0,
            login: $input['login'],
            passwordHash: password_hash($input['password'], PASSWORD_DEFAULT),
            firstName: $input['first_name'],
            lastName: $input['last_name'],
            gender: $input['gender'],
            birthDate: $input['birth_date'],
            createdAt: '',
            updatedAt: ''
        );

        return $this->repository->create($user);
    }
}