<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Core\Database;
use App\Enums\SortableUserColumns;
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
        // Second whitelist check — repository doesn't trust the caller blindly.
        // Uses the same SortableUserColumns::ALLOWED constant as GetPaginatedUsers
        // so both layers are guaranteed to stay in sync (one source of truth, two checkpoints).
        $sort   = in_array($sort, SortableUserColumns::ALLOWED, true) ? $sort : SortableUserColumns::DEFAULT;
        $dir    = strtolower($dir) === 'desc' ? 'desc' : 'asc';  // closed set of two values, no injection risk
        $offset = ($page - 1) * $perPage;

        // Safe to interpolate $sort directly: by this point it is no longer raw user input —
        // it's one of a fixed set of column name literals validated above.
        // PDO cannot bind column identifiers as parameters, so interpolation after whitelisting
        // is the correct approach here. Do not attempt to replace with a bind parameter.
        // $dir is equally safe: the ternary above guarantees it's either 'asc' or 'desc'.
        //
        // LIMIT and OFFSET are bound via bindValue(PARAM_INT) — not bindParam — because:
        //   - bindValue reads the value immediately (semantically cleaner, no dependency on
        //     the variable not changing before execute())
        //   - PARAM_INT ensures MySQL treats '10' as a number, not a string, which matters
        //     when ATTR_EMULATE_PREPARES = false (true prepared statements, as configured).
        $stmt = $this->db->prepare("
            SELECT id, login, first_name, last_name, gender, birth_date, created_at, updated_at
            FROM users
            ORDER BY `{$sort}` {$dir}
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset,  PDO::PARAM_INT);
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