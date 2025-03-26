<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $goods_id       商品ID
 * @property string                          $goods_original 商品图片
 * @property string                          $goods_thumb    商品缩略图
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage whereGoodsOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage whereGoodsThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsImage whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class GoodsImage extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
