<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserNameRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!(preg_match('/^(?!_)(?=.*[a-zA-Z])[a-zA-Z0-9_]{6,22}$/', $value) === 1)) {
            $fail('validation.user_name_validated')->translate();
        }
    }
}
