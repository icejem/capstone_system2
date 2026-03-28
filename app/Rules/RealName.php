<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RealName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim((string) $value);

        if ($value === '') {
            return;
        }

        $value = (string) preg_replace('/\s+/u', ' ', $value);

        if (preg_match('/^\d+$/', $value)) {
            $fail('Names cannot contain numbers only.');
            return;
        }

        if (! preg_match("/^(?=.*\pL)[\pL\s'-]+$/u", $value)) {
            $fail('Names should only contain letters, spaces, hyphens, or apostrophes.');
            return;
        }

        $lettersOnly = mb_strtolower((string) preg_replace('/[^\pL]/u', '', $value));
        $length = mb_strlen($lettersOnly);

        if ($length < 2) {
            $fail('Please enter a real name.');
            return;
        }

        if ($length > 50 || mb_strlen($value) > 60) {
            $fail("This doesn't look like a valid name.");
            return;
        }

        if (preg_match('/(\pL)\1{3,}/u', $lettersOnly)) {
            $fail('Please enter a real name.');
            return;
        }

        if (preg_match('/(\pL{2,4})\1{2,}/u', $lettersOnly)) {
            $fail('Please avoid random or meaningless text.');
            return;
        }

        $vowelCount = preg_match_all('/[aeiouy]/u', $lettersOnly);
        if ($length >= 4 && $vowelCount === 0) {
            $fail("This doesn't look like a valid name.");
            return;
        }

        if ($length >= 8 && ($vowelCount / max($length, 1)) < 0.23) {
            $fail('Please avoid random or meaningless text.');
            return;
        }

        $maxConsonantRun = 0;
        $currentConsonantRun = 0;

        foreach (preg_split('//u', $lettersOnly, -1, PREG_SPLIT_NO_EMPTY) as $character) {
            if (preg_match('/[aeiouy]/u', $character)) {
                $currentConsonantRun = 0;
                continue;
            }

            $currentConsonantRun++;
            $maxConsonantRun = max($maxConsonantRun, $currentConsonantRun);
        }

        if ($length >= 10 && $maxConsonantRun >= 5) {
            $fail("This doesn't look like a valid name.");
            return;
        }

        $uniqueLetterCount = count(array_unique(preg_split('//u', $lettersOnly, -1, PREG_SPLIT_NO_EMPTY)));
        if ($length >= 12 && $uniqueLetterCount <= 3) {
            $fail('Please avoid random or meaningless text.');
        }
    }
}
