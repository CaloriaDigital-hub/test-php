<?php
declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Session;

/**
 * Ensures the current request is from an authenticated administrator.
 * Redirects to the login page for HTML requests; returns 401 JSON for API requests.
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        if (Session::isLoggedIn()) {
            return;
        }

        // API routes receive a JSON 401 response instead of a redirect
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_starts_with(parse_url($uri, PHP_URL_PATH) ?? '', '/api/')) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Authentication required.']);
            exit;
        }

        header('Location: /login');
        exit;
    }
}
