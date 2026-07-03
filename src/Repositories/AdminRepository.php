<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\AdminRepositoryInterface;
use App\Core\Database;
use App\Models\AuthUser;
use PDO;

class AdminRepository implements AdminRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUsername(string $username): ?AuthUser
    {
        $stmt = $this->db->prepare(
            'SELECT id, username, password_hash
             FROM admins
             WHERE username = :username'
        );
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        return $row ? AuthUser::fromArray($row) : null;
    }
}