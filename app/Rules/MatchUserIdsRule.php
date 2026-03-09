<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MatchUserIdsRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  array  $userIds  The list of IDs that the keys must be in.
     */
    public function __construct(protected array $userIds) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $mapKeys = array_keys($value);

        $invalidIds = array_diff($mapKeys, $this->userIds);

        if (!empty($invalidIds)) {
            $fail("The following User IDs assigned to roles were not found in the users list: " . implode(', ', $invalidIds));
        }
    }
}
