<?php

namespace App\Http\Dao;

use App\Enums\ApplyRefundStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Exceptions\BusinessException;
use App\Models\ApplyRefund;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;

class ApplyRefundDao
{
    /**
     * 校验是否允许申请售后.
     *
     * @throws BusinessException
     */
    public function verifyApply(User $user, string $order_no, int $order_detail_id): void
    {
        $order = Order::query()->whereNo($order_no)->whereUserId($user->id)->first();

        if (! $order instanceof Order) {
            throw new BusinessException('订单未找到');
        }

        if ($order->pay_status < PayStatusEnum::PAYED) {
            throw new BusinessException('订单未支付');
        }

        if (! OrderDetail::query()->with('goods')->whereId($order_detail_id)->whereOrderId($order->id)->exists()) {
            throw new BusinessException('订单商品不存在');
        }

        $this->checkTimeliness($order);

        // 检测是否存在正常的售后
        $apply_refund = ApplyRefund::query()
            ->whereUserId($user->id)
            ->whereOrderId($order->id)
            ->whereOrderDetailId($order_detail_id)
            ->whereIn('status', ApplyRefund::$statusInProgressMap)
            ->orderByDesc('created_at')
            ->first();

        if ($apply_refund instanceof ApplyRefund) {
            throw new BusinessException('您已经申请过售后，请勿重复申请');
        }
    }

    /**
     * 售后超时检测.
     *
     * @throws BusinessException
     */
    public function checkTimeliness(Order $order): void
    {
        if ($order->ship_status == ShippingStatusEnum::RECEIVED) {
            // TODO 配置项：售后时效（天）
            $sale_service_time = 15;

            if (strtotime($order->received_at) + ($sale_service_time * 24 * 3600) < time()) {
                throw new BusinessException('抱歉，由于您超出售后时间，无法申请售后');
            }
        }
    }

    /**
     * 获取最大退款金额与数量.
     */
    public function getMaxAmountAndNumber(OrderDetail $order_detail, Order $order, User $user, int $apply_refund_id = 0): array
    {
        $query = ApplyRefund::query()
            ->whereUserId($user->id)
            ->whereOrderId($order_detail->order_id)
            ->whereOrderDetailId($order_detail->id);

        if ($apply_refund_id > 0) {
            // 修改申请时 不获取已拒绝的信息
            $apply_refunds = $query->whereIn('status', [
                ApplyRefundStatusEnum::NOT_PROCESSED,
                ApplyRefundStatusEnum::REFUSE_EXAMINE,
                ApplyRefundStatusEnum::BUYER_SEND_SHIP,
                ApplyRefundStatusEnum::SELLER_RECEIPT,
                ApplyRefundStatusEnum::REFUND_SUCCESS,
            ])->select(['money', 'number'])->get();
        } else {
            $apply_refunds = $query->whereIn('status', [
                ApplyRefundStatusEnum::NOT_PROCESSED,
                ApplyRefundStatusEnum::REFUSE,
                ApplyRefundStatusEnum::REFUSE_EXAMINE,
                ApplyRefundStatusEnum::BUYER_SEND_SHIP,
                ApplyRefundStatusEnum::SELLER_RECEIPT,
                ApplyRefundStatusEnum::REFUND_SUCCESS,
            ])->select(['money', 'number'])->get();
        }

        $apply_refund_money = $apply_refunds->sum('money');
        $apply_refund_number = $apply_refunds->sum('number');

        $refund_max_amount = get_new_price(bcsub(bcadd($order_detail->goods_amount, $order->shipping_fee, 2), $apply_refund_money, 2));
        $refund_max_number = get_new_price(bcsub($order_detail->goods_number, $apply_refund_number, 3));

        return [$refund_max_amount, $refund_max_number];
    }
}
