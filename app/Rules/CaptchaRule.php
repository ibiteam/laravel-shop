<?php

namespace App\Rules;

use App\Utils\NetEaseUtil;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (config('custom.net_east_yi_dun.enable')) {
            if (! $value) {
                $fail('验证失败，请刷新页面后重试！');
            } else {
                if (! app(NetEaseUtil::class)->verifyCaptcha($value)) {
                    $fail('验证失败，请刷新页面后重试~');
                }
            }
        }
    }
}
