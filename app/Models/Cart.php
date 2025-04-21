<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $user_id      用户id
 * @property int                             $goods_id     商品id
 * @property int                             $goods_sku_id 商品规格id
 * @property int                             $buy_number   购买数量
 * @property int                             $is_check     是否选中结算：1是 0否
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Goods $goods
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereBuyNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereGoodsSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereIsCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 *
 * @property-read \App\Models\GoodsSku|null $goodsSku
 *
 * @mixin \Eloquent
 */
class Cart extends BaseModel
{


    // 是否选中结算（1是 0否）
    public const IS_CHECK_YES = 1;   // 是
    public const IS_CHECK_NO = 0;    // 否

    // 无效类型
    public const INVALID_TYPE_NOT_SALE = 'status_not_sale'; // 商品下架/删除
    public const INVALID_TYPE_OUT_STOCK = 'out_of_stock'; // 商品无货

    protected $guarded = [];

    public function goods(): BelongsTo
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }

    public function goodsSku(): BelongsTo
    {
        return $this->belongsTo(GoodsSku::class, 'goods_sku_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
