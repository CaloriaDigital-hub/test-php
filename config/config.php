<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Core/EnvLoader.php';
App\Core\EnvLoader::load(__DIR__ . '/../.env');

/**
 * Configuration array.
 */
return [
    'db' => [
        'host'     => $_SERVER['DB_HOST'] ?? $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1',
        'port'     => (int)($_SERVER['DB_PORT'] ?? $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306),
        'dbname'   => $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'name',
        'username' => $_SERVER['DB_USER'] ?? $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root',
        'password' => $_SERVER['DB_PASS'] ?? $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '',
        'charset'  => $_SERVER['DB_CHARSET'] ?? $_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'app' => [
        'per_page' => (int)($_SERVER['APP_PER_PAGE'] ?? $_ENV['APP_PER_PAGE'] ?? getenv('APP_PER_PAGE') ?: 10),
        'url'      => $_SERVER['APP_URL'] ?? $_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost',
    ],
];