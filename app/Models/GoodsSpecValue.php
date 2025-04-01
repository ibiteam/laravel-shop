<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $goods_id      商品ID
 * @property int         $goods_spec_id 商品规格ID
 * @property string      $value         规格值
 * @property string      $thumb         规格图
 * @property int         $sort          排序
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read GoodsSpec $spec
 *
 * @method static Builder<static>|GoodsSpecValue newModelQuery()
 * @method static Builder<static>|GoodsSpecValue newQuery()
 * @method static Builder<static>|GoodsSpecValue query()
 * @method static Builder<static>|GoodsSpecValue whereCreatedAt($value)
 * @method static Builder<static>|GoodsSpecValue whereGoodsId($value)
 * @method static Builder<static>|GoodsSpecValue whereGoodsSpecId($value)
 * @method static Builder<static>|GoodsSpecValue whereId($value)
 * @method static Builder<static>|GoodsSpecValue whereSort($value)
 * @method static Builder<static>|GoodsSpecValue whereThumb($value)
 * @method static Builder<static>|GoodsSpecValue whereUpdatedAt($value)
 * @method static Builder<static>|GoodsSpecValue whereValue($value)
 *
 * @mixin \Eloquent
 */
class GoodsSpecValue extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    public function spec(): BelongsTo
    {
        return $this->belongsTo(GoodsSpec::class, 'goods_spec_id', 'id');
    }
}
