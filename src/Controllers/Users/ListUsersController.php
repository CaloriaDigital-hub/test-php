<?php
declare(strict_types=1);

namespace App\Controllers\Users;

// Pagination logic lives in GetPaginatedUsers UseCase, not here
// This controller just reads the request params and passes the result to the template
class ListUsersController
{
    public function __construct(
        private \App\UseCases\GetPaginatedUsers $getPaginatedUsers
    ) {}

    public function __invoke(): void
    {
        // Remember the current URL so "Back to list" from edit/show pages works correctly
        \App\Core\Session::rememberListUrl($_SERVER['REQUEST_URI']);

        $page = (int)($_GET['page'] ?? 1);
        $sort = $_GET['sort'] ?? 'login';
        $dir  = $_GET['dir'] ?? 'asc';

        $result = $this->getPaginatedUsers->execute($page, $sort, $dir);

        // Redirect only when UseCase explicitly reports it had to clamp the page.
        // Using $result --> wasClamped rather than comparing raw $page vs $result->pages
        // because the UseCase already owns this decision — don't re-derive it here.
        if ($result->wasClamped) {
            $queryParams         = $_GET;
            $queryParams['page'] = $result->currentPage;
            header('Location: /users?' . http_build_query($queryParams));
            exit;
        }

        render('users/list', [
            'users'       => $result->users,
            'total'       => $result->total,
            'pages'       => $result->pages,
            'currentPage' => $result->currentPage,
            'sort'        => $result->sort,
            'dir'         => $result->dir,
        ]);
    }
}