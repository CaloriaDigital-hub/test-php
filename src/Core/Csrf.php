<?php
declare(strict_types=1);
namespace App\Core;

/**
 * CSRF protection class.
 */
class Csrf
{
    /**
     * Generates a CSRF token.
     */
    public static function generateToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validates a CSRF token.
     */
    public static function validateToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generates a CSRF token field.
     */
    public static function getField(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . self::generateToken() . '">';
    }
}