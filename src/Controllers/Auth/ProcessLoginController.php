<?php
declare(strict_types=1);
namespace App\Controllers\Auth;

use App\Contracts\AdminRepositoryInterface;
use App\Core\Logger;
use App\Core\Session;

class ProcessLoginController
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private Logger $logger
    ) {}

    public function __invoke(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $admin = $this->adminRepository->findByUsername($username);

        // Intentionally vague error — don't tell the attacker which field was wrong
        if ($admin && password_verify($password, $admin->passwordHash)) {
            Session::login($admin->id, $admin->username);
            $this->logger->info('Admin login successful', ['username' => $admin->username]);
            header('Location: /users');
            exit;
        }

        // Log failed attempts so we can spot brute force in the logs
        $this->logger->warning('Failed login attempt', ['username' => $username]);
        render('login', ['error' => 'Invalid credentials.']);
    }
}