<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GmailAddress implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = mb_strtolower(trim((string) $value));

        if ($value === '') {
            return;
        }

        if (! str_ends_with($value, '@gmail.com')) {
            $fail('Please enter a valid Gmail address.');
        }
    }
}
