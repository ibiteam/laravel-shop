<?php

namespace App\Enums;

enum RefererEnum: string
{
    public function getLabel(): string
    {
        return match ($this) {
            self::PC => 'PC端',
            self::H5 => 'H5端',
            self::APP => 'APP端',
            self::WECHAT_MINI => '微信小程序'
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?string $source): self
    {
        return match ($source) {
            'pc' => self::PC,
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
