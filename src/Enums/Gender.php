<?php
declare(strict_types=1);

namespace App\Enums;

/**
 * Backed enum for user gender.
 * Values match the MySQL ENUM('male','female') column definition.
 */
enum Gender: string
{
    case Male   = 'male';
    case Female = 'female';

    /**
     * Human-readable label for display in the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Male   => 'Male',
            self::Female => 'Female',
        };
    }

    /**
     * All valid raw values (for validation).
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Key-value pairs for <select> options: value --> label.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = $case->label();
        }
        return $map;
    }
}
