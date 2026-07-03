<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Contracts\UserRepositoryInterface;

/**
 * JSON API endpoint for paginated user listing.
 * Returns structured JSON with proper error handling.
 */
class ListUsersController
{
    public function __construct(
        private \App\UseCases\GetPaginatedUsers $getPaginatedUsers
    ) {}

    public function __invoke(): void
    {
        header('Content-Type: application/json');

        try {
            // Save the non-API equivalent URL so "Back to list" works for AJAX loaded pages too
            $frontendUrl = str_replace('/api/users', '/users', $_SERVER['REQUEST_URI']);
            \App\Core\Session::rememberListUrl($frontendUrl);

            $page = (int)($_GET['page'] ?? 1);
            $sort = $_GET['sort'] ?? 'login';
            $dir  = $_GET['dir'] ?? 'asc';

            $result = $this->getPaginatedUsers->execute($page, $sort, $dir);

            // Convert UserListItem objects to arrays for JSON serialization
            $usersArray = array_map(function ($user) {
                return [
                    'id'         => $user->id,
                    'login'      => $user->login,
                    'firstName'  => $user->firstName,
                    'lastName'   => $user->lastName,
                    'gender'     => $user->gender,
                    'birthDate'  => $user->birthDate,
                    'createdAt'  => $user->createdAt,
                    'updatedAt'  => $user->updatedAt,
                ];
            }, $result->users);

            echo json_encode([
                'users'          => $usersArray,
                'total'          => $result->total,
                'pages'          => $result->pages,
                'currentPage'    => $result->currentPage,
                'sort'           => $result->sort,
                'dir'            => $result->dir,
                // True when the requested page was out of range and was silently clamped.
                // The JS client should use this to sync the browser URL via history.pushState.
                'pageWasAdjusted' => $result->wasClamped,
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error.']);
        }
    }
}
