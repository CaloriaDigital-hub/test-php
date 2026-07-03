<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Core\Database;
use App\Models\User;
use App\Models\UserListItem;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * @return UserListItem[]
     */
    public function getPaginatedList(int $page, string $sort, string $dir, int $perPage): array
    {
        // Defence-in-depth: validate sort/dir even though GetPaginatedUsers already did it upstream.
        // Both whitelists must stay in sync — see GetPaginatedUsers::resolveSortColumn.
        $allowed = ['id', 'login', 'first_name', 'last_name', 'gender', 'birth_date', 'created_at', 'updated_at'];
        $sort = in_array($sort, $allowed, true) ? $sort : 'login';
        $dir  = strtolower($dir) === 'desc' ? 'desc' : 'asc';
        $offset = ($page - 1) * $perPage;

        // $sort is safe (whitelisted above), so inline interpolation here is fine.
        // LIMIT/OFFSET go through bindValue because PDO can't parameterize those.
        $stmt = $this->db->prepare("
            SELECT id, login, first_name, last_name, gender, birth_date, created_at, updated_at
            FROM users
            ORDER BY `{$sort}` {$dir}
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        return array_map(fn($row) => UserListItem::fromArray($row), $rows);
    }

    public function countAll(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    /**
     * Returns a read-only projection (UserListItem) for display in list/detail views.
     * Does NOT include the password hash — use this for any output to the user.
     */
    public function findByIdForDisplay(int $id): ?UserListItem
    {
        $stmt = $this->db->prepare(
            'SELECT id, login, first_name, last_name, gender, birth_date, created_at, updated_at
             FROM users WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? UserListItem::fromArray($row) : null;
    }

    /**
     * Returns the full User entity including all fields.
     * Use this when you need to populate an edit form or pass data into a write UseCase.
     */
    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare(
            'SELECT id, login, password_hash, first_name, last_name, gender, birth_date, created_at, updated_at
             FROM users WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? User::fromArray($row) : null;
    }

    // excludeId is used during update so we don't flag the user's own login as taken
    public function loginExists(string $login, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE login = :login';
        $params = ['login' => $login];
        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetchColumn();
    }

    public function create(User $user): int
    {
        $data = $user->toInsertArray();
        $stmt = $this->db->prepare('
            INSERT INTO users (login, password_hash, first_name, last_name, gender, birth_date)
            VALUES (:login, :password_hash, :first_name, :last_name, :gender, :birth_date)
        ');
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, User $user): bool
    {
        $data = $user->toInsertArray();
        $data['id'] = $id;

        $stmt = $this->db->prepare('
            UPDATE users
            SET login = :login,
                password_hash = :password_hash,
                first_name = :first_name,
                last_name = :last_name,
                gender = :gender,
                birth_date = :birth_date
            WHERE id = :id
        ');
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}