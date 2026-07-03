<?php
declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Csrf;
use App\Core\Logger;
use App\Core\Session;

class LogoutController
{
    public function __construct(
        private Logger $logger
    ) {}

    public function __invoke(): void
    {
        // Validate CSRF token — prevents <img src="/logout"> style forced-logout attacks
        if (!Csrf::validateToken($_POST['_csrf_token'] ?? '')) {
            http_response_code(403);
            echo 'Invalid request.';
            exit;
        }

        // Read username before destroying the session — it won't be available after
        $username = $_SESSION['admin_username'] ?? 'unknown';
        Session::logout();
        $this->logger->info('Admin logged out', ['username' => $username]);
        header('Location: /login');
        exit;
    }
}