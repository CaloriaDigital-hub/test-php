<?php
declare(strict_types=1);

namespace App\Enums;

// Single source of truth for sortable columns.
// Referenced in both GetPaginatedUsers (UseCase layer) and UserRepository (data layer).
// Two independent checks remain — repository doesn't trust the caller blindly —
// but they can't silently diverge because they share this list.
final class SortableUserColumns
{
    public const ALLOWED = ['id', 'login', 'first_name', 'last_name', 'gender', 'birth_date', 'created_at', 'updated_at'];
    public const DEFAULT = 'login';
}
