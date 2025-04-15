<?php

namespace App\Http\Dao;

use App\Models\ApplyRefund;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;

class TransactionDao
{
    /**
     * 支付订单生成流水.
     */
    public function storeByOrder(Order $order, Payment $payment, string $remark = ''): Transaction
    {
        return Transaction::query()->create([
            'transaction_no' => $this->generateTransactionNo('order'),
            'user_id' => $order->user_id,
            'parent_id' => 0,
            'transaction_type' => Transaction::TRANSACTION_TYPE_PAY,
            'type' => $order::class,
            'type_id' => $order->id,
            'payment_id' => $payment->id,
            'amount' => $order->order_amount,
            'status' => Transaction::STATUS_WAIT,
            'remark' => $remark,
        ]);
    }

    /**
     * 退款订单生成流水.
     */
    public function storeByRefund(ApplyRefund $apply_refund, Transaction $transaction, string $remark = ''): Transaction
    {
        return Transaction::query()->create([
            'transaction_no' => $this->generateRefundNo(),
            'user_id' => $apply_refund->user_id,
            'transaction_type' => Transaction::TRANSACTION_TYPE_REFUND,
            'parent_id' => $transaction->id,
            'type' => $transaction->type,
            'type_id' => $transaction->type_id,
            'payment_id' => $transaction->payment_id,
            'amount' => -$apply_refund->money,  // 退款金额记负数
            'status' => Transaction::STATUS_WAIT,
            'remark' => $remark,
        ]);
    }

    /**
     * 生成退款单号.
     */
    public function generateRefundNo(): string
    {
        return $this->generateTransactionNo('refund');
    }

    /**
     * 生成交易流水号.
     */
    private function generateTransactionNo(string $prefix): string
    {
        return $prefix.'_'.get_flow_sn();
    }
}
