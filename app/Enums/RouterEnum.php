<?php

namespace App\Enums;

enum RouterEnum: string
{
    public function getLabel(): string
    {
        return match ($this) {
            self::PAY_SUCCESS => '支付成功',
            self::ORDER_SUCCESS => '下单成功',
            self::HOME_PREVIEW => '首页预览',
            self::SUPERMARKET => '多多超市',
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
            'order_success' => self::ORDER_SUCCESS,
            'home_preview' => self::HOME_PREVIEW,
            'supermarket' => self::SUPERMARKET,
        };
    }

    case PAY_SUCCESS = 'pay_success';
    case ORDER_SUCCESS = 'order_success';
    case SUPERMARKET = 'supermarket'; // 超市
    case HOME_PREVIEW = 'home_preview'; // 首页预览
}
