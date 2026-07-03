<?php
declare(strict_types=1);

namespace App\Exceptions;

class NotFoundException extends \RuntimeException
{
    public function __construct(string $entity = 'Resource', int $id = 0)
    {
        parent::__construct("{$entity} with id {$id} not found.");
    }
}
