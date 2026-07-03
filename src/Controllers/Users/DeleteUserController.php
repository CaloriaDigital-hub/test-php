<?php
declare(strict_types=1);

namespace App\Controllers\Users;

use App\Core\Csrf;
use App\Core\Logger;
use App\UseCases\DeleteUser;
use RuntimeException;

class DeleteUserController
{
    public function __construct(
        private DeleteUser $deleteUser,
        private Logger $logger
    ) {}

    public function __invoke(int $id): void
    {
        // Don't even try to delete without a valid CSRF token
        if (!Csrf::validateToken($_POST['_csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'CSRF token mismatch';
            exit;
        }

        try {
            $this->deleteUser->execute($id);
            $this->logger->info('User deleted', ['id' => $id]);

            // Go back to wherever the user was in the list (same page, same filters)
            $redirectUrl = \App\Core\Session::getLastListUrl();
            \App\Core\Session::setFlash('success', 'User deleted successfully.');

            header('Location: ' . $redirectUrl);
            exit;
        } catch (RuntimeException $e) {
            // User probably doesn't exist, or something went wrong at DB level
            http_response_code(404);
            render('errors/404');
        }
    }
}