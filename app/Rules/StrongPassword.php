<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = (string) $value;

        if (! preg_match('/[a-z]/', $password)) {
            $fail('Password must include at least one lowercase letter (a-z).');

            return;
        }

        if (! preg_match('/[A-Z]/', $password)) {
            $fail('Password must include at least one uppercase letter (A-Z).');

            return;
        }

        if (! preg_match('/\d/', $password)) {
            $fail('Password must include at least one number (0-9).');

            return;
        }

        if (! preg_match('/[^A-Za-z0-9]/', $password)) {
            $fail('Password must include at least one special character (e.g., !@#$%^&*).');
        }
    }
}
