<?php
declare(strict_types=1);

namespace App\Models;
readonly class User
{
    public function __construct(
        public int $id,
        public string $login,
        public string $passwordHash,
        public string $firstName,
        public string $lastName,
        public string $gender,
        public string $birthDate,
        public string $createdAt,
        public string $updatedAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['login'],
            $data['password_hash'],
            $data['first_name'],
            $data['last_name'],
            $data['gender'],
            $data['birth_date'],
            $data['created_at'],
            $data['updated_at']
        );
    }


    /**
     * @return array{login: string, password_hash: string, first_name: string, last_name: string, gender: string, birth_date: string}
     */
    public function toInsertArray(): array
    {
        return [
            'login'         => $this->login,
            'password_hash' => $this->passwordHash,
            'first_name'    => $this->firstName,
            'last_name'     => $this->lastName,
            'gender'        => $this->gender,
            'birth_date'    => $this->birthDate,
        ];
    }
}