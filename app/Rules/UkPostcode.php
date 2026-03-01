<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UkPostcode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // https://github.com/stemount/gov-uk-official-postcode-regex-helper
        $regex = '/^(([A-Z][0-9]{1,2})|(([A-Z][A-HJ-Y][0-9]{1,2})|(([A-Z][0-9][A-Z])|([A-Z][A-HJ-Y][0-9]?[A-Z]))))\s?[0-9][A-Z]{2}$/i';

        if (! preg_match($regex, $value)) {
            $fail('The :attribute must be a valid UK postcode.');
        }
    }
}
