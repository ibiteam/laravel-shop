<?php

namespace App\Services\Pay;

use App\Enums\PayFormEnum;
use App\Models\Order;
use App\Models\PaymentMethod;

interface PayOrderInterface
{
    public function orderPay(Order $order, PaymentMethod $payment_method, PayFormEnum $pay_form_enum);
}
