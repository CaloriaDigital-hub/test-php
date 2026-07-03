<?php
declare(strict_types=1);
namespace App\Core;

/**
 * Simple regex-based router with middleware support.
 */
class Router
{
    private array $routes = [];

    /** @var MiddlewareInterface[] Middleware applied via group() to subsequent routes */
    private array $groupMiddleware = [];

    /**
     * Register a route with optional middleware.
     *
     * @param string   $method     HTTP method (GET, POST, etc.)
     * @param string   $path       URI pattern, e.g. /users/{id}
     * @param callable $handler    Controller or closure
     * @param MiddlewareInterface[] $middleware Per-route middleware
     */
    public function add(string $method, string $path, callable $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method'     => strtoupper($method),
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    /**
     * Apply shared middleware to all routes registered inside the callback.
     *
     * @param MiddlewareInterface[] $middleware
     * @param callable              $callback   fn(Router) — register routes inside
     */
    public function group(array $middleware, callable $callback): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);
        $callback($this);
        $this->groupMiddleware = $previous;
    }

    /**
     * Match the current request to a route and execute it.
     */
    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri    = parse_url($uri, PHP_URL_PATH);
        $uri    = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Convert route pattern to regex: /users/{id} --> /users/(?P<id>[^/]+)
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Run middleware chain
                foreach ($route['middleware'] as $mw) {
                    $mw->handle();
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                call_user_func($route['handler'], $params);
                return;
            }
        }

        // No route matched
        http_response_code(404);
        render('errors/404');
    }
}