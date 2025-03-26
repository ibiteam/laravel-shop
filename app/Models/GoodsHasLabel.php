<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $goods_id       商品ID
 * @property int                             $goods_label_id 商品标签ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsHasLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsHasLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsHasLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsHasLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsHasLabel whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsHasLabel whereGoodsLabelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsHasLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsHasLabel whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class GoodsHasLabel extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
