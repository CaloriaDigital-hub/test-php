<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$pdo = App\Core\Database::getInstance();
$schemaFile = __DIR__ . '/../database/schema.sql';

if (!file_exists($schemaFile)) {
    echo "Schema file not found.\n";
    exit(1);
}

$sql = file_get_contents($schemaFile);

try {
    $pdo->exec($sql);
    echo "Database migrated successfully.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
