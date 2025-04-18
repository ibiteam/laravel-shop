<?php

namespace App\Enums;

enum PayPrefixEnum: string
{
    case USER_CANCEL_ORDER = 'cancel_order';
    case APPLY_REFUND = 'apply_refund';
    case MANAGE_REFUND = 'manage_refund';
    case USER_PAY_ORDER = 'order';
}
