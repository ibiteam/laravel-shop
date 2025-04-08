<?php

namespace App\Enums;

use App\Exceptions\BusinessException;

enum PayFormEnum: string
{
    public function getLabel(): string
    {
        return match ($this) {
            self::PAY_FORM_APP => 'app支付',
            self::PAY_FORM_MINI => '小程序支付',
            self::PAY_FORM_WECHAT => '微信网页版',
            self::PAY_FORM_H5 => 'h5支付'
        };
    }

    /**
     * @throws BusinessException
     */
    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    /**
     * @throws BusinessException
     */
    public static function formSource(?string $source): self
    {
        return match ($source) {
            'app' => self::PAY_FORM_APP,
            'mini' => self::PAY_FORM_MINI,
            'wechat' => self::PAY_FORM_WECHAT,
            'h5' => self::PAY_FORM_H5,
            default => throw new BusinessException('类型错误'),
        };
    }
    case PAY_FORM_APP = 'app'; // app支付
    case PAY_FORM_MINI = 'mini'; // 小程序支付
    case PAY_FORM_WECHAT = 'wechat'; // 微信网页版
    case PAY_FORM_H5 = 'h5'; // h5支付
}
