<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $user_id    用户ID
 * @property int         $goods_id   商品ID
 * @property string|null $referer    来源 pc、app、h5、mini
 * @property string|null $ip         用户IP地址
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at 删除时间
 * @property-read Goods|null $goods
 * @property-read User|null $user
 *
 * @method static Builder<static>|GoodsView newModelQuery()
 * @method static Builder<static>|GoodsView newQuery()
 * @method static Builder<static>|GoodsView onlyTrashed()
 * @method static Builder<static>|GoodsView query()
 * @method static Builder<static>|GoodsView whereCreatedAt($value)
 * @method static Builder<static>|GoodsView whereDeletedAt($value)
 * @method static Builder<static>|GoodsView whereGoodsId($value)
 * @method static Builder<static>|GoodsView whereId($value)
 * @method static Builder<static>|GoodsView whereIp($value)
 * @method static Builder<static>|GoodsView whereReferer($value)
 * @method static Builder<static>|GoodsView whereUpdatedAt($value)
 * @method static Builder<static>|GoodsView whereUserId($value)
 * @method static Builder<static>|GoodsView withTrashed()
 * @method static Builder<static>|GoodsView withoutTrashed()
 *
 * @mixin \Eloquent
 */
class GoodsView extends BaseModel
{
    use SoftDeletes;

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
