<?php
declare(strict_types=1);

// --- Error & Environment ---
// Never expose errors to the browser in production.
// Errors are logged via Logger.php instead.
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// --- Autoloader ---
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

// --- Load environment ---
require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/EnvLoader.php';
App\Core\EnvLoader::load(__DIR__ . '/../.env');

// --- Session Security ---
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly'  => true,
    'samesite' => 'Lax',
]);
session_start();

// --- Load configuration & initialize core services ---
$config = require __DIR__ . '/../config/config.php';
App\Core\Database::init($config['db']);
$logger = App\Core\Logger::getInstance();

// --- Pure DI ---
$adminRepo = new App\Repositories\AdminRepository();
$userRepo  = new App\Repositories\UserRepository();

$createUserValidator = new App\Validators\CreateUserValidator();
$updateUserValidator = new App\Validators\UpdateUserValidator();

$createUser = new App\UseCases\CreateUser($userRepo, $createUserValidator);
$updateUser = new App\UseCases\UpdateUser($userRepo, $updateUserValidator);
$deleteUser = new App\UseCases\DeleteUser($userRepo);

// Auth Controllers
$loginFormCtrl    = new App\Controllers\Auth\LoginFormController();
$processLoginCtrl = new App\Controllers\Auth\ProcessLoginController($adminRepo, $logger);
$logoutCtrl       = new App\Controllers\Auth\LogoutController($logger);

// User Controllers
$perPage = (int) $config['app']['per_page'];
$getPaginatedUsers = new App\UseCases\GetPaginatedUsers($userRepo, $perPage);

$listUsersCtrl  = new App\Controllers\Users\ListUsersController($getPaginatedUsers);
$showUserCtrl   = new App\Controllers\Users\ShowUserController($userRepo);
$createFormCtrl = new App\Controllers\Users\CreateUserFormController();
$storeUserCtrl  = new App\Controllers\Users\StoreUserController($createUser, $logger);
$editFormCtrl   = new App\Controllers\Users\EditUserFormController($userRepo);
$updateUserCtrl = new App\Controllers\Users\UpdateUserController($updateUser, $userRepo, $logger);
$deleteUserCtrl = new App\Controllers\Users\DeleteUserController($deleteUser, $logger);
$apiListUsersCtrl = new App\Controllers\Api\ListUsersController($getPaginatedUsers);

// --- Router ---
$router = new App\Core\Router();
$authMw = new App\Core\Middleware\AuthMiddleware();

// Public routes (no auth required)
$router->add('GET',  '/login',  $loginFormCtrl);
$router->add('POST', '/login',  $processLoginCtrl);
$router->add('GET',  '/logout', $logoutCtrl);

// Protected routes (auth required via middleware)
$router->group([$authMw], function (App\Core\Router $r) use (
    $listUsersCtrl, $createFormCtrl, $storeUserCtrl,
    $showUserCtrl, $editFormCtrl, $updateUserCtrl,
    $deleteUserCtrl, $apiListUsersCtrl
) {
    $r->add('GET',  '/users',               $listUsersCtrl);
    $r->add('GET',  '/users/create',        $createFormCtrl);
    $r->add('POST', '/users',               $storeUserCtrl);
    $r->add('GET',  '/users/{id}',          fn($p) => $showUserCtrl->__invoke((int)$p['id']));
    $r->add('GET',  '/users/{id}/edit',     fn($p) => $editFormCtrl->__invoke((int)$p['id']));
    $r->add('POST', '/users/{id}',          fn($p) => $updateUserCtrl->__invoke((int)$p['id']));
    $r->add('POST', '/users/{id}/delete',   fn($p) => $deleteUserCtrl->__invoke((int)$p['id']));
    $r->add('GET',  '/api/users',           $apiListUsersCtrl);
});

// Root redirect
$router->add('GET', '/', function() { header('Location: /users'); exit; });

// --- Dispatch ---
try {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $router->dispatch($_SERVER['REQUEST_METHOD'], $uri);
} catch (\App\Exceptions\NotFoundException $e) {
    http_response_code(404);
    render('errors/404');
} catch (\Throwable $e) {
    $logger->error('Unhandled exception', [
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
    ]);
    http_response_code(500);
    // Never leak exception details to the browser in production.
    echo 'Internal Server Error';
}