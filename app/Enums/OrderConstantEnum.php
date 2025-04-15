<?php

namespace App\Enums;

enum OrderConstantEnum: int
{
    public function getLabel(): string
    {
        return match ($this) {
            self::STATUS_NOT_CONFIRM => '待确认',
            self::STATUS_CANCELLED => '已取消',
            self::STATUS_WAIT_PAY => '待付款',
            self::STATUS_WAIT_SHIP => '待发货',
            self::STATUS_WAIT_RECEIVE => '待收货',
            self::STATUS_PART => '部分发货',
        };
    }

    public static function getLabelBySource(string $status): string
    {
        return self::formSource($status)->getLabel();
    }

    public static function formSource(?int $status): self
    {
        return match ($status) {
            1 => self::STATUS_NOT_CONFIRM,
            2 => self::STATUS_CANCELLED,
            3 => self::STATUS_WAIT_PAY,
            4 => self::STATUS_WAIT_SHIP,
            5 => self::STATUS_WAIT_RECEIVE,
            6 => self::STATUS_SUCCESS,
            7 => self::STATUS_PART,
        };
    }

    case STATUS_NOT_CONFIRM = 1; // 待确认
    case STATUS_CANCELLED = 2; // 已取消
    case STATUS_WAIT_PAY = 3; // 待付款
    case STATUS_WAIT_SHIP = 4; // 待发货
    case STATUS_WAIT_RECEIVE = 5; // 待收货
    case STATUS_SUCCESS = 6; // 已完成
    case STATUS_PART = 7; // 部分发货
}
