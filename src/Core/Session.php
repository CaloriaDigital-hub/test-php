<?php
declare(strict_types=1);

namespace App\Core;

class Session
{
    /**
     * Start session if not started
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn(): bool
    {
        self::start(); // Autostart session in case isn't running
        return isset($_SESSION['admin_id']);
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public static function login(int $id, string $username): void
    {
        self::start();
        // Safety against session fixation
        session_regenerate_id(true); 
        $_SESSION['admin_id'] = $id;
        $_SESSION['admin_username'] = $username;
    }

    /**
     * Remembers list url for 
     */
    public static function rememberListUrl(string $url): void
    {
        self::start();
        $_SESSION['last_list_url'] = $url;
    }
    
    /**
     * Returns last list url
     */
    public static function getLastListUrl(): string
    {
        self::start();
        return $_SESSION['last_list_url'] ?? '/users';
    }

    /**
     * Set flash message for one request
     */
    public static function setFlash(string $key, string $message): void
    {
        self::start();
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get flash message and unset it
    */
    public static function getFlash(string $key): ?string
    {
        self::start();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            
            // Clear flash array if empty
            if (empty($_SESSION['flash'])) {
                unset($_SESSION['flash']);
            }
            
            return $message;
        }
        return null;
    }

    /**
     * Full and safe destroy session
     */
    public static function logout(): void
    {
        self::start();

        // 1. Clear the $_SESSION array on the server
        $_SESSION = [];

        // 2. Delete the session cookie from the client's browser
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // 3. Destroy the session on the server
        session_destroy();
    }
}
