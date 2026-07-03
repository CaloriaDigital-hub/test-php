<?php
declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Logger;
use App\Core\Session;

class LogoutController
{
    public function __construct(
        private Logger $logger
    ) {}

    public function __invoke(): void
    {
        $username = $_SESSION['admin_username'] ?? 'unknown';
        Session::logout();
        $this->logger->info('Admin logged out', ['username' => $username]);
        header('Location: /login');
        exit;
    }
}