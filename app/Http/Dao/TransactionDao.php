<?php

namespace App\Http\Dao;

use App\Enums\PayPrefixEnum;
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
            'transaction_no' => $this->generateTransactionNo(PayPrefixEnum::USER_PAY_ORDER),
            'user_id' => $order->user_id,
            'parent_id' => 0,
            'transaction_type' => Transaction::TRANSACTION_TYPE_PAY,
            'type' => $order::class,
            'type_id' => $order->id,
            'payment_id' => $payment->id,
            'amount' => $order->order_amount,
            'status' => Transaction::STATUS_WAIT,
            'remark' => $remark,
            'can_refund' => true,
        ]);
    }

    /**
     * 退款生成流水.
     *
     * @param Transaction $parent_transaction 父级交易流水
     * @param string      $transaction_no     退款单号
     * @param int|float   $refund_amount      退款金额
     * @param int         $status             退款状态
     * @param string      $remark             退款备注
     */
    public function storeByParentTransaction(Transaction $parent_transaction, string $transaction_no, int|float $refund_amount, int $status = Transaction::STATUS_WAIT, string $remark = ''): Transaction
    {
        return Transaction::query()->create([
            'transaction_no' => $transaction_no,
            'user_id' => $parent_transaction->user_id,
            'transaction_type' => Transaction::TRANSACTION_TYPE_REFUND,
            'parent_id' => $parent_transaction->id,
            'type' => $parent_transaction->type,
            'type_id' => $parent_transaction->type_id,
            'payment_id' => $parent_transaction->payment_id,
            'amount' => -$refund_amount,  // 退款金额记负数
            'status' => $status,
            'remark' => $remark,
            'can_refund' => false,
        ]);
    }

    /**
     * 生成交易流水号.
     */
    public function generateTransactionNo(PayPrefixEnum $pay_prefix_enum): string
    {
        return $pay_prefix_enum->value.'_'.get_flow_sn();
    }
}
