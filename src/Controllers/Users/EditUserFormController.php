<?php
declare(strict_types=1);
namespace App\Controllers\Users;

use App\Contracts\UserRepositoryInterface;

class EditUserFormController
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(int $id): void
    {

        $user = $this->userRepository->findById($id);
        if (!$user) {
            http_response_code(404);
            render('errors/404');
            return;
        }

        render('users/form', [
            'user'   => $user,
            'errors' => [],
            'old'    => [],
        ]);
    }
}