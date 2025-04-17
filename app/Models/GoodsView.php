<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $goods_id 商品ID
 * @property string|null $referer 来源 pc、app、h5、mini
 * @property string|null $ip 用户IP地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView whereUserId($value)
 * @property-read \App\Models\Goods|null $goods
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsView withoutTrashed()
 * @property-read \App\Models\User|null $user
 * @mixin \Eloquent
 */
class GoodsView extends Model
{
    use DatetimeTrait,SoftDeletes;

    protected $guarded = [];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
