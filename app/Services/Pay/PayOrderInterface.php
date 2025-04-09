<?php

namespace App\Services\Pay;

use App\Enums\PayFormEnum;
use App\Models\Order;
use App\Models\Payment;

interface PayOrderInterface
{
    public function orderPay(Order $order, Payment $payment, PayFormEnum $pay_form_enum);
}
