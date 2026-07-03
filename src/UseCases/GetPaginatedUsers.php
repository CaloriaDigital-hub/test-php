<?php
declare(strict_types=1);

namespace App\UseCases;

use App\Contracts\UserRepositoryInterface;

// Handles pagination, sorting, and page clamping in one place.
// Both the HTML controller and the API controller delegate to this — no duplication.
final class GetPaginatedUsers
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private int $perPage,
    ) {}

    public function execute(int $requestedPage, string $sort, string $dir): PaginatedUsersResult
    {
        $total = $this->userRepository->countAll();
        $pages = (int) ceil($total / $this->perPage);

        // Never go below page 1
        $page = max(1, $requestedPage);
        $wasClamped = false;

        // If someone requested page 99 but only 3 pages exist, land them on page 3
        if ($pages > 0 && $page > $pages) {
            $page = $pages;
            $wasClamped = true;
        }

        $sort = $this->resolveSortColumn($sort);
        $dir  = strtolower($dir) === 'desc' ? 'desc' : 'asc';

        $users = $this->userRepository->getPaginatedList($page, $sort, $dir, $this->perPage);

        return new PaginatedUsersResult($users, $total, $pages, $page, $sort, $dir, $wasClamped);
    }

    private function resolveSortColumn(string $sort): string
    {
        // Whitelist to prevent ORDER BY injection — PDO can't parameterize column names
        $allowed = ['id', 'login', 'first_name', 'last_name', 'gender', 'birth_date', 'created_at', 'updated_at'];
        return in_array($sort, $allowed, true) ? $sort : 'login';
    }
}