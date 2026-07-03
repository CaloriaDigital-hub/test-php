<?php
declare(strict_types=1);
namespace App\Controllers\Auth;


class LoginFormController
{
    public function __invoke(): void
    {
        render('login');
    }
}