<?php

namespace App\Http\Dao;

use App\Enums\PaymentEnum;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class PaymentDao
{
    /**
     * 获取有效支付方式列表.
     */
    public function getEffectiveList(): EloquentCollection|Collection
    {
        return Payment::query()
            ->latest('is_recommend')
            ->latest('sort')
            ->whereIsEnabled(true)
            ->select(['name', 'alias', 'is_recommend'])
            ->get();
    }

    public function getListByEnabled(bool $is_enabled = true): EloquentCollection|Collection
    {
        return Payment::query()
            ->latest('is_recommend')
            ->latest('sort')
            ->whereIsEnabled($is_enabled)
            ->select(['name', 'alias', 'icon', 'description', 'limit', 'is_recommend', 'sort'])
            ->get();
    }

    public function getInfoByAlias(PaymentEnum $payment_enum): ?Payment
    {
        return Payment::query()->whereAlias($payment_enum->value)->first();
    }
}
