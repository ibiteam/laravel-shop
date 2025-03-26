<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $goods_id      商品ID
 * @property int                             $goods_spec_id 商品规格ID
 * @property string                          $value         规格值
 * @property string                          $thumb         规格图
 * @property int                             $sort          排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue whereGoodsSpecId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSpecValue whereValue($value)
 *
 * @mixin \Eloquent
 */
class GoodsSpecValue extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
