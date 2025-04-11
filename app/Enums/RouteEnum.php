<?php

namespace App\Enums;

enum RouteEnum: string
{
    public function getLabel(): string
    {
        return match ($this) {
            self::PAY_SUCCESS => '支付成功',
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?string $source): self
    {
        return match ($source) {
            'pay_success' => self::PAY_SUCCESS,
            default => self::PAY_SUCCESS,
        };
    }
    case PAY_SUCCESS = 'pay_success';
}
