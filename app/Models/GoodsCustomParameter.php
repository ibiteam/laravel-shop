<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $goods_id   商品ID
 * @property string                          $name       参数名称
 * @property string                          $value      参数值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsCustomParameter whereValue($value)
 *
 * @mixin \Eloquent
 */
class GoodsCustomParameter extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
