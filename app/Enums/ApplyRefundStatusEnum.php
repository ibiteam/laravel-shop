<?php

namespace App\Enums;

enum ApplyRefundStatusEnum: int
{
    public function getLabel(): string
    {
        return match ($this) {
            self::NOT_PROCESSED => '待处理',
            self::REFUSE => '已拒绝退款',
            self::REFUSE_EXAMINE => '退货审核成功 待买家发货',
            self::BUYER_SEND_SHIP => '买家已发货 待卖家收货',
            self::SELLER_RECEIPT => '卖家已收货',
            self::REFUND_SUCCESS => '退款成功',
            self::REFUND_CLOSE => '退款关闭',
        };
    }

    public static function getLabelBySource(string $source): string
    {
        return self::formSource($source)->getLabel();
    }

    public static function formSource(?int $source): self
    {
        return match ($source) {
            0 => self::NOT_PROCESSED,
            1 => self::REFUSE,
            2 => self::REFUSE_EXAMINE,
            3 => self::BUYER_SEND_SHIP,
            4 => self::SELLER_RECEIPT,
            5 => self::REFUND_SUCCESS,
            6 => self::REFUND_CLOSE,
        };
    }

    case NOT_PROCESSED = 0; // 待处理
    case REFUSE = 1; // 已拒绝退款
    case REFUSE_EXAMINE = 2; // 退货审核成功 待买家发货
    case BUYER_SEND_SHIP = 3; // 买家已发货 待卖家收货
    case SELLER_RECEIPT = 4; // 卖家已收货
    case REFUND_SUCCESS = 5; // 退款成功
    case REFUND_CLOSE = 6; // 退款关闭
}
