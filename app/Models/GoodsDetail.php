<?php

namespace App\Models;




/**
 * @property int                             $id
 * @property int                             $goods_id   商品ID
 * @property string                          $content    商品详细内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsDetail whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsDetail whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class GoodsDetail extends BaseModel
{


    protected $guarded = [];
}
