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
            self::SPECIAL_PAGE => '专题页',
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?string $source): ?self
    {
        return match ($source) {
            'pay_success' => self::PAY_SUCCESS,
            'order_success' => self::ORDER_SUCCESS,
            'home_preview' => self::HOME_PREVIEW,
            'special_page' => self::SPECIAL_PAGE,
            default => null
        };
    }

    case PAY_SUCCESS = 'pay_success';
    case ORDER_SUCCESS = 'order_success';
    case SPECIAL_PAGE = 'special_page'; // 专题页
    case HOME_PREVIEW = 'home_preview'; // 首页预览
}
