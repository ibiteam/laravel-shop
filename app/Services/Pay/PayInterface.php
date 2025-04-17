<?php

namespace App\Services\Pay;

use App\Enums\PayFormEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;

interface PayInterface
{
    public function orderPay(Order $order, Payment $payment, PayFormEnum $pay_form_enum);

    public function refund(Transaction $parent_transaction, string $refund_transaction_no, Payment $payment, int|float $refund_amount, string $reason): array;
}
