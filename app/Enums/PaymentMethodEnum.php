<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    public function getLabel(): string
    {
        return match ($this) {
            self::WECHAT => '微信支付',
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?string $source): self
    {
        return match ($source) {
            'wechat' => self::WECHAT,
            default => self::WECHAT,
        };
    }
    case WECHAT = 'wechat';
}
