<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Contract for route-level middleware.
 *
 * Implementations should throw an exception or send an HTTP response
 * (redirect, JSON error) and exit when the request must be blocked.
 */
interface MiddlewareInterface
{
    /**
     * Process incoming request.
     * Must call exit() or throw if request should not proceed.
     */
    public function handle(): void;
}
