<?php
/**
 * bin/create-admin.php – Create a new admin user.
 * Usage: php bin/create-admin.php [username] [password]
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$pdo = App\Core\Database::getInstance();

// Read arguments or prompt
$username = $argv[1] ?? null;
$password = $argv[2] ?? null;

if ($username === null || $password === null) {
    echo "Username: ";
    $username = trim(fgets(STDIN));
    echo "Password: ";
    $password = trim(fgets(STDIN));
}

if (empty($username) || empty($password)) {
    echo "Username and password cannot be empty.\n";
    exit(1);
}

// Check if already exists
$stmt = $pdo->prepare('SELECT COUNT(*) FROM admins WHERE username = :username');
$stmt->execute(['username' => $username]);
if ($stmt->fetchColumn() > 0) {
    echo "Admin '{$username}' already exists.\n";
    exit(0);
}

// Insert new admin
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (:username, :hash)');
$stmt->execute(['username' => $username, 'hash' => $hash]);

echo "Admin '{$username}' created successfully.\n";