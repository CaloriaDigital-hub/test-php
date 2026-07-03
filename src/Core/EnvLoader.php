<?php
declare(strict_types=1);
namespace App\Core;

use RuntimeException;

/**
 * To avoid depending on external libraries, I decided to write my own env loader.
 */

class EnvLoader
{
    /**
     * Load .env file into environment variables.
     * Does not overwrite existing variables by default.
     */
    public static function load(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Env file not found: {$filePath}"); // typos existed
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Split on first '=' sign
            if (!str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2); // limit=2 in case of '=' in value
            $name  = trim($name);
            $value = trim($value);

            // Remove surrounding quotes if present
            if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
                $value = substr($value, 1, -1);
            } elseif (str_starts_with($value, "'") && str_ends_with($value, "'")) {
                $value = substr($value, 1, -1);
            }

            // Set environment variable if not already set externally
            if (getenv($name) === false) {
                putenv("{$name}={$value}");
                $_ENV[$name]    = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}