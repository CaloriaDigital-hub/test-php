<?php
declare(strict_types=1);

namespace App\UseCases;

class PaginatedUsersResult
{
    /**
     * @param array $users Array of UserListItem
     * @param bool $wasClamped True if the requested page exceeded the total and was adjusted to the last valid page.
     */
    public function __construct(
        public readonly array $users,
        public readonly int $total,
        public readonly int $pages,
        public readonly int $currentPage,
        public readonly string $sort,
        public readonly string $dir,
        public readonly bool $wasClamped = false,
    ) {}
}
