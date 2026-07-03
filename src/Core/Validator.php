<?php
declare(strict_types=1);
namespace App\Core;

use DateTime;

// Simple fluent validator — call rules in sequence, then check errors() at the end.
// Each rule overwrites the previous error for the same field, so order matters.
class Validator
{
    private array $errors = [];

    // Treats "   " (whitespace-only) as empty too
    public function required(string $field, mixed $value, string $label = ''): void
    {
        if (empty(trim((string)$value))) {
            $this->errors[$field] = ($label ?: $field) . ' is required.';
        }
    }

    // mb_strlen so multibyte characters count as 1 character, not as raw bytes
    public function minLength(string $field, string $value, int $min): void
    {
        if (mb_strlen($value) < $min) {
            $this->errors[$field] = "Must be at least {$min} characters.";
        }
    }

    public function maxLength(string $field, string $value, int $max): void
    {
        if (mb_strlen($value) > $max) {
            $this->errors[$field] = "Must not exceed {$max} characters.";
        }
    }

    // Caller supplies both the regex and the human-readable message
    public function pattern(string $field, string $value, string $regex, string $message): void
    {
        if (!preg_match($regex, $value)) {
            $this->errors[$field] = $message;
        }
    }

    // Two-step: first check format, then check if the date actually exists.
    // createFromFormat alone isn't enough — it accepts "2024-02-30" without errors.
    public function date(string $field, string $value): void
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $this->errors[$field] = 'Date must be YYYY-MM-DD.';
            return;
        }
        $d = DateTime::createFromFormat('Y-m-d', $value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            $this->errors[$field] = 'Invalid date.';
            return;
        }
        // Reasonable range: 1900 – current year
        $year = (int)$d->format('Y');
        if ($year < 1900 || $year > (int)date('Y')) {
            $this->errors[$field] = 'Birth date must be between 1900 and today.';
        }
    }

    // Used for enum-like fields (e.g. gender) — strict comparison so '1' != 1
    public function inArray(string $field, string $value, array $allowed): void
    {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field] = 'Invalid value.';
        }
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}