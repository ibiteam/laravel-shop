<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $user_id      用户id
 * @property int                             $goods_id     商品id
 * @property int                             $is_attention 是否关注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Goods $goods
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect whereIsAttention($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCollect whereUserId($value)
 *
 * @mixin \Eloquent
 */
class GoodsCollect extends BaseModel
{


    // 关注状态
    public const ATTENTION_YES = 1; // 关注
    public const ATTENTION_NO = 0; // 取消关注

    protected $guarded = [];

    public function goods(): BelongsTo
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
