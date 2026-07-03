<?php
declare(strict_types=1);
namespace App\Controllers\Users;

use App\Core\Csrf;
use App\Core\Logger;
use App\Exceptions\ValidationException;
use App\UseCases\CreateUser;

class StoreUserController
{
    public function __construct(
        private CreateUser $createUser,
        private Logger $logger
    ) {}

    public function __invoke(): void
    {
        if (!Csrf::validateToken($_POST['_csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'CSRF token mismatch';
            exit;
        }

        try {
            $id = $this->createUser->execute($_POST);
            $this->logger->info('User created', ['id' => $id, 'login' => $_POST['login'] ?? '']);
            \App\Core\Session::setFlash('success', 'User created successfully.');
            header('Location: /users/' . $id);
            exit;
        } catch (ValidationException $e) {
            render('users/form', [
                'user'   => null,
                'errors' => $e->getErrors(),
                'old'    => $_POST,
            ]);
        }
    }
}