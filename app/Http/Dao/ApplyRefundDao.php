<?php

namespace App\Http\Dao;

use App\Enums\ApplyRefundStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Enums\PayPrefixEnum;
use App\Exceptions\BusinessException;
use App\Models\ApplyRefund;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\ShopConfig;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Pay\PayService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplyRefundDao
{
    /**
     * 根据用户获取申请退款列表(分页).
     */
    public function getListByUser(User $user, string $keywords = '', int $page = 1, int $number = 10): LengthAwarePaginator
    {
        return ApplyRefund::query()->with(['user', 'order', 'orderDetail', 'orderDetail.goods'])
            ->when($keywords, function (Builder $query) use ($keywords) {
                $query->where(function (Builder $query) use ($keywords) {
                    $query->whereLike('no', "%$keywords%")->orWhereHas('orderDetail', function (Builder $query) use ($keywords) {
                        $query->whereLike('goods_name', "%$keywords%");
                    });
                });
            })
            ->whereUserId($user->id)
            ->orderByDesc('created_at')
            ->paginate($number, page: $page);
    }

    /**
     * 校验是否允许申请售后.
     *
     * @throws BusinessException
     */
    public function verifyApply(User $user, string $order_sn, int $order_detail_id): void
    {
        $order = Order::query()->whereOrderSn($order_sn)->whereUserId($user->id)->first();

        if (! $order instanceof Order) {
            throw new BusinessException('订单未找到');
        }

        if ($order->pay_status < PayStatusEnum::PAYED->value) {
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
        if ($order->ship_status == ShippingStatusEnum::RECEIVED->value) {
            // 售后时效（天）
            $after_sales_timeliness = intval(shop_config(ShopConfig::AFTER_SALES_TIMELINESS));

            if ($after_sales_timeliness && strtotime($order->received_at) + ($after_sales_timeliness * 24 * 3600) < time()) {
                throw new BusinessException('抱歉，由于您超出售后时间，无法申请售后');
            }
        }
    }

    /**
     * 申请售后 按钮状态.
     */
    public function showAfterSales(Order $order, OrderDetail $order_detail): int
    {
        // 按钮状态：0不显示，1显示售后按钮，2售后退款中，3售后退款成功
        $after_sales_button_status = 0;

        $is_show_after_sales = intval(shop_config(ShopConfig::IS_SHOW_AFTER_SALES));

        if ($is_show_after_sales) {
            $after_sales_max_money = floatval(shop_config(ShopConfig::AFTER_SALES_MAX_MONEY));

            if ($order->order_amount <= $after_sales_max_money) {
                // 成功支付记录
                $pay_success_transaction = $order->transactions()
                    ->where('transaction_type', Transaction::TRANSACTION_TYPE_PAY)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->first();

                if ($pay_success_transaction) {
                    $after_sales_button_status = 1;

                    // 这里看订单下指定明细的
                    $apply_refunding = ApplyRefund::query()
                        ->whereOrderId($order->id)->whereOrderDetailId($order_detail->id)
                        ->whereIn('status', ApplyRefund::$statusInProgressMap)
                        ->orderByDesc('created_at')->first();

                    if ($apply_refunding) {
                        $after_sales_button_status = 2;
                    } else {
                        $apply_refund = ApplyRefund::query()
                            ->whereOrderId($order->id)->whereOrderDetailId($order_detail->id)
                            ->whereStatus(ApplyRefundStatusEnum::REFUND_SUCCESS->value)
                            ->select(['money', 'number'])->get();
                        $success_money = $apply_refund->sum('money');
                        $success_number = $apply_refund->sum('number');

                        if ($success_number >= $order_detail->goods_number && $success_money >= $order_detail->goods_amount) {
                            $after_sales_button_status = 3;
                        }
                    }
                }
            }
        }

        return $after_sales_button_status;
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
                ApplyRefundStatusEnum::NOT_PROCESSED->value,
                ApplyRefundStatusEnum::REFUSE_EXAMINE->value,
                ApplyRefundStatusEnum::BUYER_SEND_SHIP->value,
                ApplyRefundStatusEnum::SELLER_RECEIPT->value,
                ApplyRefundStatusEnum::REFUND_SUCCESS->value,
            ])->select(['money', 'number'])->get();
        } else {
            $apply_refunds = $query->whereIn('status', [
                ApplyRefundStatusEnum::NOT_PROCESSED->value,
                ApplyRefundStatusEnum::REFUSE->value,
                ApplyRefundStatusEnum::REFUSE_EXAMINE->value,
                ApplyRefundStatusEnum::BUYER_SEND_SHIP->value,
                ApplyRefundStatusEnum::SELLER_RECEIPT->value,
                ApplyRefundStatusEnum::REFUND_SUCCESS->value,
            ])->select(['money', 'number'])->get();
        }

        $apply_refund_money = $apply_refunds->sum('money');
        $apply_refund_number = $apply_refunds->sum('number');

        $refund_max_amount = get_new_price(bcsub(bcadd($order_detail->goods_amount, $order->shipping_fee, 2), $apply_refund_money, 2));
        $refund_max_number = get_new_price(bcsub($order_detail->goods_number, $apply_refund_number, 3));

        return [$refund_max_amount, $refund_max_number];
    }

    /**
     * 退款交易检测.
     *
     * @throws BusinessException
     */
    public function refundTransactionCheck(ApplyRefund $apply_refund)
    {
        // 支付成功订单
        $order = Order::query()->with(['transactions'])
            ->whereId($apply_refund->order_id)
            ->wherePayStatus(PayStatusEnum::PAYED->value)
            ->first();

        if (! $order instanceof Order) {
            throw new BusinessException('订单未支付成功');
        }

        // 获取成功的支付交易记录
        $pay_success_transaction = $order->transactions
            ->where('transaction_type', Transaction::TRANSACTION_TYPE_PAY)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->first();

        if (! $pay_success_transaction instanceof Transaction) {
            throw new BusinessException('未找到成功的交易记录');
        }

        // 查询已有的成功退款交易记录
        $refund_success_transactions = Transaction::query()
            ->where('parent_id', $pay_success_transaction->id)
            ->where('transaction_type', Transaction::TRANSACTION_TYPE_REFUND)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->get();

        if ($apply_refund->money <= 0 || $pay_success_transaction->amount <= 0) {
            throw new BusinessException('退款金额或支付金额必须为正数');
        }

        if ($refund_success_transactions->isNotEmpty()) {
            if ($apply_refund->money > $pay_success_transaction->amount + $refund_success_transactions->sum('amount')) {
                throw new BusinessException('累计退款总金额超过支付金额');
            }
        } else {
            if ($apply_refund->money > $pay_success_transaction->amount) {
                throw new BusinessException('退款金额超过支付金额');
            }
        }

        return $pay_success_transaction;
    }

    /**
     * 根据订单与用户获取正在进行中的申请售后数据.
     */
    public function getProgressDataByOrder(Order $order, User $user): EloquentCollection|Collection
    {
        return ApplyRefund::query()->whereOrderId($order->id)->whereUserId($user->id)->get();
    }

    /**
     * 微信退款.
     *
     * @throws BusinessException|\Throwable
     */
    public function wechatRefund(ApplyRefund $apply_refund): void
    {
        try {
            DB::beginTransaction();

            $pay_success_transaction = $this->refundTransactionCheck($apply_refund);

            // 微信退款
            $out_refund_no = app(TransactionDao::class)->generateTransactionNo(PayPrefixEnum::APPLY_REFUND);

            $payment = Payment::query()->whereId($pay_success_transaction->payment_id)->first();

            $result = PayService::init($payment->alias)->refund(
                $pay_success_transaction,
                $out_refund_no,
                $payment,
                $apply_refund->money,
                $apply_refund->applyRefundReason?->content
            );

            if (! $result['transaction'] ?? null) {
                throw new BusinessException('退款生成流水失败');
            }

            $apply_refund->update(['transaction_id' => $result['transaction']->id ?? 0]);

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error('申请售后微信退款异常: '.$exception->getMessage());

            throw new BusinessException('申请售后微信退款异常');
        }
    }
}
