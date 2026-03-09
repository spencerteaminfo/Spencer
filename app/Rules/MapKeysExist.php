<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class MapKeysExist implements ValidationRule
{
    /**
     * @param string $table The table to check (e.g., 'users')
     * @param string $column The column to check (e.g., 'id')
     */
    public function __construct(
        protected string $table,
        protected string $column = 'id'
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("The {$attribute} must be a map.");
            return;
        }

        $keys = array_keys($value);

        $existsCount = DB::table($this->table)
            ->whereIn($this->column, $keys)
            ->count();

        if ($existsCount !== count($keys)) {
            $fail("One or more keys in {$attribute} are invalid or do not exist in the {$this->table} table.");
        }
    }
}
