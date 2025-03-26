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
        if (!(preg_match('/^(?!_)(?=.*[a-zA-Z])[a-zA-Z0-9_]{3,22}$/', $value) === 1)) {
            $fail('用户名3-22个字符，支持字母（区分大小写）、数字、下划线，不支持以下划线开头，不能设置为纯数字');
        }
    }
}
