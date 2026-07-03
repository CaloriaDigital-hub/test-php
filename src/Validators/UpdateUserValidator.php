<?php
declare(strict_types=1);
namespace App\Validators;

use App\Core\Validator;
use App\Enums\Gender;

class UpdateUserValidator
{
    public function validate(array $data): array
    {
        $v = new Validator();
        $v->required('login', $data['login'] ?? null);
        $v->maxLength('login', $data['login'] ?? '', 100);
        $v->pattern('login', $data['login'] ?? '', '/^[a-zA-Z0-9_]+$/', 'Login may only contain letters, digits and underscores.');
        $v->required('first_name', $data['first_name'] ?? null);
        $v->maxLength('first_name', $data['first_name'] ?? '', 100);
        $v->required('last_name', $data['last_name'] ?? null);
        $v->maxLength('last_name', $data['last_name'] ?? '', 100);
        $v->inArray('gender', $data['gender'] ?? '', Gender::values());
        $v->required('birth_date', $data['birth_date'] ?? null);
        $v->date('birth_date', $data['birth_date'] ?? '');

        if (!empty($data['password'])) {
            $v->minLength('password', $data['password'], 6);
            $v->maxLength('password', $data['password'], 255);
        }

        return $v->errors();
    }
}