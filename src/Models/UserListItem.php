<?php
declare(strict_types=1);

namespace App\Models;

final readonly class UserListItem
{
    public function __construct(
        public int    $id,
        public string $login,
        public string $firstName,
        public string $lastName,
        public string $gender,
        public string $birthDate,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['login'],
            $data['first_name'],
            $data['last_name'],
            $data['gender'],
            $data['birth_date'],
            $data['created_at'],
            $data['updated_at'],
        );
    }
}