<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Simple file-based logger with daily rotation and structured output.
 *
 * Log format: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message {"context":"data"}
 * Files are stored in the project's logs/ directory, one file per day.
 */
class Logger
{
    private static ?self $instance = null;
    private string $logDir;

    private function __construct(string $logDir)
    {
        $this->logDir = rtrim($logDir, '/\\');

        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0755, true);
        }
    }

    /**
     * Get or create the singleton Logger instance.
     * Uses the project-level logs/ directory by default.
     */
    public static function getInstance(?string $logDir = null): self
    {
        if (self::$instance === null) {
            $dir = $logDir ?? dirname(__DIR__, 2) . '/logs';
            self::$instance = new self($dir);
        }
        return self::$instance;
    }

    /**
     * Replace the singleton instance (useful for testing).
     */
    public static function setInstance(self $logger): void
    {
        self::$instance = $logger;
    }

    /**
     * Logging debug messages
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Logging info messages
     */

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }


    /**
     * Logging warning messages
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Logging error messages
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Write a single log entry to the daily log file.
     */
    private function log(string $level, string $message, array $context): void
    {
        $date = date('Y-m-d');
        $time = date('Y-m-d H:i:s');
        $file = $this->logDir . "/app-{$date}.log";

        $entry = "[{$time}] [{$level}] {$message}";
        if (!empty($context)) {
            $entry .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $entry .= PHP_EOL;

        @file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
    }
}
