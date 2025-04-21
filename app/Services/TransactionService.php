<?php

namespace App\Services;

use App\Enums\ApplyRefundStatusEnum;
use App\Enums\PayPrefixEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\ApplyRefundDao;
use App\Models\ApplyRefund;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function wechatRefund(Transaction $transaction): void
    {
        $transaction->update(['status' => Transaction::STATUS_SUCCESS, 'paid_at' => now()->toDateTimeString()]);

        /* 用户手动取消订单，在支付回调减钱 */
        if (str_starts_with($transaction->transaction_no, PayPrefixEnum::USER_CANCEL_ORDER->value)) {
            $this->refundByUserCancelOrder($transaction);
        } elseif (str_starts_with($transaction->transaction_no, PayPrefixEnum::MANAGE_REFUND->value)) {
            $this->refundByManageRefund($transaction);
        } elseif (str_starts_with($transaction->transaction_no, PayPrefixEnum::APPLY_REFUND->value)) {
            $this->refundByApplyRefund($transaction);
        }
    }

    /**
     * 用户手动取消订单，在支付回调减钱.
     */
    private function refundByUserCancelOrder(Transaction $transaction): void
    {
        $order = Order::query()->whereId($transaction->type_id)->first();

        if ($order instanceof Order) {
            $order->update(['money_paid' => 0]);
        }
    }

    /**
     * 管理员退款，在支付回调减钱.
     */
    private function refundByManageRefund(Transaction $transaction): void
    {
        switch ($transaction->type) {
            case Order::class:
                $order = Order::query()->whereId($transaction->type_id)->first();

                if ($order instanceof Order) {
                    $order->update(['money_paid' => 0]);
                }

                break;
        }
    }

    /**
     * 售后退款，在支付回调减钱.
     */
    private function refundByApplyRefund(Transaction $transaction): void
    {
        $apply_refund = ApplyRefund::query()->whereTransactionId($transaction->id)->first();

        if ($apply_refund instanceof ApplyRefund) {

            try {
                // 申请售后退款成功 逻辑处理
                app(OrderOperateService::class)->applyRefund($apply_refund);
            } catch (BusinessException $business_exception) {
                Log::error('支付回调，处理申请售后逻辑报错：'.$business_exception->getMessage(), $business_exception->getTrace());
            } catch (\Throwable $exception) {
                Log::error('支付回调，处理申请售后逻辑异常：'.$exception->getMessage(), $exception->getTrace());
            }
        }
    }
}
