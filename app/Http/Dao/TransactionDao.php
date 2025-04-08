<?php

namespace App\Http\Dao;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Transaction;

class TransactionDao
{
    /**
     * 支付订单生成流水.
     */
    public function storeByOrder(Order $order, PaymentMethod $payment_method): Transaction
    {
        return Transaction::query()->create([
            'transaction_no' => 'order_'.get_flow_sn(),
            'user_id' => $order->user_id,
            'parent_id' => 0,
            'transaction_type' => Transaction::TRANSACTION_TYPE_PAY,
            'type' => $order::class,
            'type_id' => $order->id,
            'payment_method_id' => $payment_method->id,
            'amount' => $order->order_amount,
            'status' => Transaction::STATUS_WAIT,
        ]);
    }
}
