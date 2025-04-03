<?php

namespace App\Enums;

use Exception;

enum RefferEnum: string
{
    case PC = 'pc';
    case H5 = 'h5';
    case APP = 'app';
    case WECHAT_MINI = 'wechat_mini';

    public function getLabel(): string
    {
        return match ($this) {
            self::PC => "pc",
            self::H5 => "h5",
            self::APP => "app",
            self::WECHAT_MINI => "微信小程序"
        };
    }

    public static function getValue(string $value): string
    {
        $enum = match($value) {
            'pc' => self::PC,
            'h5' => self::H5,
            'app' => self::APP,
            'wechat_mini' => self::WECHAT_MINI,
            default => throw new Exception("Invalid status: $value"),
        };

        return $enum->getLabel();
    }
}
