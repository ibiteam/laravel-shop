<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $seller_id  入驻商家ID
 * @property string                          $name       规格名
 * @property string                          $value      规格值
 * @property int                             $is_show    是否展示 1展示 0不展示
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SellerShop|null $shopInfo
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpec whereValue($value)
 *
 * @mixin \Eloquent
 */
class GoodsSpec extends Model
{
    use DatetimeTrait;
    public const SHOW = 1; // 展示
    public const NOT_SHOW = 0; // 不展示

    protected $guarded = [];

    public function shopInfo(): BelongsTo
    {
        return $this->belongsTo(SellerShop::class, 'seller_id', 'seller_id');
    }
}
