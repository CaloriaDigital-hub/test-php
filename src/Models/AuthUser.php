<?php
declare(strict_types=1);


namespace App\Models;

final readonly class AuthUser
{
    public function __construct(
        public int    $id,
        public string $username,
        public string $passwordHash,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['username'],
            $data['password_hash'],
        );
    }
}