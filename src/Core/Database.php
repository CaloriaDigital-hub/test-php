<?php
declare(strict_types=1);
namespace App\Core;

use PDO;
use RuntimeException;

/**
 * PDO singleton with explicit two-step initialization.
 *
 * Call Database::init($config) once in the bootstrap (public/index.php).
 * After that, any class can call Database::getInstance() to get the connection.
 *
 * Why a Singleton and not constructor injection?
 * For a project of this scale (2 repositories) a Singleton is a pragmatic trade-off:
 * it avoids passing $pdo through every constructor manually while still guaranteeing
 * a single connection per request. In a larger project the PDO instance would be
 * wired explicitly through the DI container and this class would not exist.
 *
 * init() and getInstance() are intentionally separate (no lazy-init inside getInstance).
 * If bootstrap forgets to call init(), the caller gets an immediate, descriptive
 * RuntimeException instead of a confusing failure deep inside a repository.
 *
 * Use setInstance() to inject a mock/test PDO without touching bootstrap.
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Initialize the PDO connection from a config array.
     * Should be called once during application bootstrap.
     *
     * @param array{host: string, port: int, dbname: string, username: string, password: string, charset: string} $config
     */
    public static function init(array $config): void
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
        self::$instance = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // throw on error, not silently return false
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // arrays by column name, not by index
            PDO::ATTR_EMULATE_PREPARES   => false,                   // real prepared statements, not emulated
        ]);
    }

    /**
     * Override the PDO instance (useful for testing).
     */
    public static function setInstance(PDO $pdo): void
    {
        self::$instance = $pdo;
    }

    /**
     * Get the current PDO instance.
     *
     * @throws RuntimeException if init() has not been called
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            throw new RuntimeException('Database not initialized. Call Database::init() first.');
        }
        return self::$instance;
    }
}