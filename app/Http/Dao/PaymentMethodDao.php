<?php

namespace App\Http\Dao;

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
}
