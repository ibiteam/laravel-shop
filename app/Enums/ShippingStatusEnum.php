<?php

namespace App\Enums;

enum ShippingStatusEnum: int
{
    public function getLabel(): string
    {
        return match ($this) {
            self::UNSHIPPED => '未发货',
            self::SHIPPED => '已发货',
            self::RECEIVED => '已收货',
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?int $source): self
    {
        return match ($source) {
            0 => self::UNSHIPPED,
            1 => self::SHIPPED,
            2 => self::RECEIVED,
        };
    }
    case UNSHIPPED = 0; // 未发货
    case SHIPPED = 1; // 已发货
    case RECEIVED = 2; // 已收货
}
