<?php

namespace App\Enums;

enum PayStatusEnum: int
{
    public function getLabel(): string
    {
        return match ($this) {
            self::PAY_WAIT => '未付款',
            self::PAYED => '已付款',
            self::PARTIAL => '已付款(部分)',
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?int $source): self
    {
        return match ($source) {
            0 => self::PAY_WAIT,
            1 => self::PAYED,
            2 => self::PARTIAL,
        };
    }
    case PAY_WAIT = 0; // 未支付
    case PAYED = 1; // 已支付
    case PARTIAL = 2; // 部分支付
}
