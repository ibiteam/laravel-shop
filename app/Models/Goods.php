<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property int                             $seller_id             入驻商家ID
 * @property int                             $category_id           分类ID
 * @property int                             $seller_category_id    商家分类ID
 * @property int                             $brand_id              品牌ID
 * @property string                          $goods_sn              商品编号
 * @property string                          $goods_name            商品标题
 * @property string                          $goods_sub_name        商品副标题
 * @property string                          $keywords              商品关键词
 * @property string                          $price                 商品价格
 * @property int                             $number                商品库存
 * @property int                             $buy_min_number        最小起订量
 * @property int                             $check_status          审核状态 1审核通过 2审核驳回 0未审核
 * @property string|null                     $check_status_datetime 审核时间
 * @property int                             $status                上下架状态 1上架 0下架
 * @property int                             $sort                  排序，值越大越靠前
 * @property string                          $goods_original        商品主图
 * @property string                          $goods_thumb           商品缩略图
 * @property string|null                     $video                 视频地址
 * @property int                             $video_duration        视频时长
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Brand $brand
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\GoodsDetail|null $detail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GoodsImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GoodsLabel> $labels
 * @property-read int|null $labels_count
 * @property-read \App\Models\SellerShop|null $shopInfo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GoodsSku> $skus
 * @property-read int|null $skus_count
 *
 * @method static Builder<static>|Goods newModelQuery()
 * @method static Builder<static>|Goods newQuery()
 * @method static Builder<static>|Goods onlyTrashed()
 * @method static Builder<static>|Goods query()
 * @method static Builder<static>|Goods show()
 * @method static Builder<static>|Goods whereBrandId($value)
 * @method static Builder<static>|Goods whereBuyMinNumber($value)
 * @method static Builder<static>|Goods whereCategoryId($value)
 * @method static Builder<static>|Goods whereCheckStatus($value)
 * @method static Builder<static>|Goods whereCheckStatusDatetime($value)
 * @method static Builder<static>|Goods whereCreatedAt($value)
 * @method static Builder<static>|Goods whereDeletedAt($value)
 * @method static Builder<static>|Goods whereGoodsName($value)
 * @method static Builder<static>|Goods whereGoodsOriginal($value)
 * @method static Builder<static>|Goods whereGoodsSn($value)
 * @method static Builder<static>|Goods whereGoodsSubName($value)
 * @method static Builder<static>|Goods whereGoodsThumb($value)
 * @method static Builder<static>|Goods whereId($value)
 * @method static Builder<static>|Goods whereKeywords($value)
 * @method static Builder<static>|Goods whereNumber($value)
 * @method static Builder<static>|Goods wherePrice($value)
 * @method static Builder<static>|Goods whereSellerCategoryId($value)
 * @method static Builder<static>|Goods whereSellerId($value)
 * @method static Builder<static>|Goods whereSort($value)
 * @method static Builder<static>|Goods whereStatus($value)
 * @method static Builder<static>|Goods whereUpdatedAt($value)
 * @method static Builder<static>|Goods whereVideo($value)
 * @method static Builder<static>|Goods whereVideoDuration($value)
 * @method static Builder<static>|Goods withTrashed()
 * @method static Builder<static>|Goods withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Goods extends Model
{
    use DatetimeTrait, SoftDeletes;
    public const CHECK_STATUS_WAIT = 0; // 未审核
    public const CHECK_STATUS_YES = 1; // 审核通过
    public const CHECK_STATUS_NO = 2; // 审核驳回
    public const STATUS_ON_SALE = 1; // 上架
    public const STATUS_NOT_SALE = 0; // 下架

    protected $guarded = [];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(GoodsDetail::class, 'goods_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(GoodsImage::class, 'goods_id', 'id');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(GoodsSku::class, 'goods_id', 'id');
    }

    public function shopInfo(): BelongsTo
    {
        return $this->belongsTo(SellerShop::class, 'seller_id', 'seller_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(GoodsLabel::class, (new GoodsHasLabel)->getTable(), 'goods_id', 'goods_label_id');
    }

    public function scopeShow(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ON_SALE)->where('check_status', self::CHECK_STATUS_YES);
    }
}
