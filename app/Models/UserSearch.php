<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $user_id    用户id
 * @property string|null                     $goods_ids  搜索的商品id
 * @property string|null                     $keywords   搜索关键词
 * @property string|null                     $source     来源
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch whereGoodsIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSearch whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserSearch extends BaseModel
{


    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
