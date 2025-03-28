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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameter whereValue($value)
 *
 * @mixin \Eloquent
 */
class GoodsParameter extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
