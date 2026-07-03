<?php
declare(strict_types=1);
namespace App\Core;

use PDO;
use RuntimeException;

/**
 * PDO singleton with explicit initialization.
 *
 * Call Database::init($config) once at bootstrap.
 * Use Database::setInstance($pdo) to inject a mock/test connection.
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