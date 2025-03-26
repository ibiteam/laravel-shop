<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $seller_id  入驻商家ID
 * @property string                          $name       模板名称
 * @property string                          $values     模板值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SellerShop|null $shopInfo
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereValues($value)
 *
 * @mixin \Eloquent
 */
class GoodsSkuTemplate extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    public function shopInfo(): BelongsTo
    {
        return $this->belongsTo(SellerShop::class, 'seller_id', 'seller_id');
    }
}
