<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int                             $id
 * @property int                             $seller_id        入驻商家ID
 * @property string                          $name             标签名称
 * @property string                          $type             标签类型：1文字标签 2图片标签
 * @property string|null                     $image            图片标签地址
 * @property string|null                     $font_color       文字颜色
 * @property string|null                     $background_color 背景颜色
 * @property string|null                     $border_color     边框颜色
 * @property int                             $is_show          是否显示 1显示 0不显示
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Goods> $goods
 * @property-read int|null $goods_count
 * @property-read \App\Models\SellerShop|null $shopInfo
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereBorderColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereFontColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsLabel whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class GoodsLabel extends Model
{
    use DatetimeTrait;
    public const SHOW = 1; // 展示
    public const NOT_SHOW = 0; // 不展示
    public const TYPE_TEXT = 1; // 文字标签
    public const TYPE_IMAGE = 2; // 图片标签

    protected $guarded = [];

    public function shopInfo(): BelongsTo
    {
        return $this->belongsTo(SellerShop::class, 'seller_id', 'seller_id');
    }

    public function goods(): BelongsToMany
    {
        return $this->belongsToMany(Goods::class, (new GoodsHasLabel)->getTable(), 'goods_label_id', 'goods_id');
    }
}
