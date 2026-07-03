<?php
declare(strict_types=1);

namespace App\Controllers\Users;

class CreateUserFormController
{
    public function __invoke(): void
    {
        render('users/form', [
            'user'   => null,
            'errors' => [],
            'old'    => [],
        ]);
    }
}