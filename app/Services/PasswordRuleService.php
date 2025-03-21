<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordRuleService
{
    private static int $min_length = 8;

    private static int $max_length = 30;

    public static function userPasswordRule(): Password
    {
        // 最少8位字符 最多30位字符 必须包含数字和字母(不区分大小写) 特殊字符只能包含：#@$%&*-+_~<>.
        return Password::min(self::$min_length)
            ->max(self::$max_length)
            ->letters()
            ->numbers()
            ->rules(['regex:/^[a-zA-Z0-9#@$%&*\-+_~<>.]+$/u']);
    }

    public static function generatePassword(): string
    {
        $allChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#@$%&_';
        $length = rand(self::$min_length, self::$max_length);

        do {
            $password = '';

            for ($i = 0; $i < $length; $i++) {
                $password .= $allChars[rand(0, strlen($allChars) - 1)];
            }
            $validator = Validator::make(['password' => $password], ['password' => ['required', self::userPasswordRule()]]);
        } while ($validator->fails());

        return $password;
    }
}
