<?php

namespace App\Enums;

enum RefererEnum: string
{
    public function getLabel(): string
    {
        return match ($this) {
            self::PC => 'pc',
            self::H5 => 'h5',
            self::APP => 'app',
            self::WECHAT_MINI => '微信小程序'
        };
    }

    public static function getValue(string $value): string
    {
        $enum = self::formString($value);

        return $enum->getLabel();
    }

    public static function formString(string $value): self
    {
        return match ($value) {
            'pc' => self::PC,
            'h5' => self::H5,
            'app' => self::APP,
            'wechat_mini' => self::WECHAT_MINI,
            default => self::H5,
        };
    }

    case PC = 'pc';
    case H5 = 'h5';
    case APP = 'app';
    case WECHAT_MINI = 'wechat_mini';
}
