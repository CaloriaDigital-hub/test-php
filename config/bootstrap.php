<?php
// config/bootstrap.php – shared init for web and CLI

declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

// Autoloader
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// .env
require_once __DIR__ . '/../src/Core/EnvLoader.php';
App\Core\EnvLoader::load(__DIR__ . '/../.env');

// Config
$config = require __DIR__ . '/config.php';

// PDO initialization
\App\Core\Database::init($config['db']);

return $config;