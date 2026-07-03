<?php
declare(strict_types=1);

namespace App\Exceptions;
use RuntimeException;

class ValidationException extends RuntimeException
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation failed');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}