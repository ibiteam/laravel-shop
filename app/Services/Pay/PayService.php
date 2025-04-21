<?php

namespace App\Services\Pay;

use App\Enums\PaymentEnum;
use App\Exceptions\BusinessException;

class PayService
{
    public static function init(string $alias): PayInterface
    {
        return match (PaymentEnum::formSource($alias)) {
            PaymentEnum::WECHAT => new WechatPayService,
            default => throw new BusinessException('支付类型错误'),
        };
    }
}
