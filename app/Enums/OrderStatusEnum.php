<?php

namespace App\Enums;

enum OrderStatusEnum: int
{
    public function getLabel(): string
    {
        return match ($this) {
            self::UNCONFIRMED => '未确认',
            self::CONFIRMED => '已确认',
            self::CANCELLED => '已取消',
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?int $source): self
    {
        return match ($source) {
            0 => self::UNCONFIRMED,
            1 => self::CONFIRMED,
            2 => self::CANCELLED,
        };
    }
    case UNCONFIRMED = 0;
    case CONFIRMED = 1;
    case CANCELLED = 2;
}
