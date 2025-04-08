<?php

namespace App\Http\Dao;

use App\Enums\PaymentMethodEnum;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class PaymentMethodDao
{
    /**
     * 获取有效支付方式列表.
     */
    public function getEffectiveList(): EloquentCollection|Collection
    {
        return PaymentMethod::query()
            ->latest('is_recommend')
            ->latest('sort')
            ->whereIsEnabled(true)
            ->select(['name', 'alias', 'is_recommend'])
            ->get();
    }

    public function getListByEnabled(bool $is_enabled = true): EloquentCollection|Collection
    {
        return PaymentMethod::query()
            ->latest('is_recommend')
            ->latest('sort')
            ->whereIsEnabled($is_enabled)
            ->select(['name', 'alias', 'icon', 'description', 'limit', 'is_recommend', 'sort'])
            ->get();
    }

    public function getInfoByAlias(PaymentMethodEnum $payment_method_enum): ?PaymentMethod
    {
        return PaymentMethod::query()->whereAlias($payment_method_enum->value)->first();
    }
}
