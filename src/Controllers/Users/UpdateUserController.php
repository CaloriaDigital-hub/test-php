<?php
declare(strict_types=1);
namespace App\Controllers\Users;

use App\Contracts\UserRepositoryInterface;
use App\Core\Csrf;
use App\Core\Logger;
use App\Exceptions\ValidationException;
use App\UseCases\UpdateUser;

class UpdateUserController
{
    /**
     * Constructor with DI
     */
    public function __construct(
        private UpdateUser $updateUser,
        private UserRepositoryInterface $userRepository,
        private Logger $logger
    ) {}

    /**
     * Handle POST request to update user
     */
    public function __invoke(int $id): void
    {
        
        // Validate CSRF token
        if (!Csrf::validateToken($_POST['_csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'CSRF token mismatch';
            exit;
        }

        try {
            $this->updateUser->execute($id, $_POST);
            $this->logger->info('User updated', ['id' => $id, 'login' => $_POST['login'] ?? '']);
            \App\Core\Session::setFlash('success', 'User updated successfully.');
            header('Location: /users/' . $id);
            exit;
        } catch (ValidationException $e) {
            $user = $this->userRepository->findById($id);
            render('users/form', [
                'user'   => $user,
                'errors' => $e->getErrors(),
                'old'    => $_POST,
            ]);
        }
    }
}
