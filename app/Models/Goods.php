<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property int                             $category_id     分类ID
 * @property string                          $no              商品编号
 * @property string                          $name            商品标题
 * @property string                          $sub_name        商品副标题
 * @property string                          $label           商品副标题
 * @property string                          $image           商品主图
 * @property string                          $keywords        商品关键词
 * @property string                          $price           商品价格
 * @property int                             $total           商品库存
 * @property int                             $buy_min_number  最小起订量
 * @property int                             $type            库存类型 1下单减库存 2付款减库存
 * @property int                             $status          上下架状态 1上架 0下架
 * @property string|null                     $status_datetime 上下架时间
 * @property int                             $can_quota       是否限购 1限购 0不限购
 * @property int                             $quota_number    限购数量
 * @property int                             $sort            排序，值越大越靠前
 * @property string|null                     $video           视频地址
 * @property int                             $video_duration  视频时长
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\GoodsDetail|null $detail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GoodsImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GoodsParameter> $parameters
 * @property-read int|null $parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GoodsSku> $skus
 * @property-read int|null $skus_count
 *
 * @method static Builder<static>|Goods newModelQuery()
 * @method static Builder<static>|Goods newQuery()
 * @method static Builder<static>|Goods onlyTrashed()
 * @method static Builder<static>|Goods query()
 * @method static Builder<static>|Goods show()
 * @method static Builder<static>|Goods whereBuyMinNumber($value)
 * @method static Builder<static>|Goods whereCanQuota($value)
 * @method static Builder<static>|Goods whereCategoryId($value)
 * @method static Builder<static>|Goods whereCreatedAt($value)
 * @method static Builder<static>|Goods whereDeletedAt($value)
 * @method static Builder<static>|Goods whereId($value)
 * @method static Builder<static>|Goods whereImage($value)
 * @method static Builder<static>|Goods whereKeywords($value)
 * @method static Builder<static>|Goods whereLabel($value)
 * @method static Builder<static>|Goods whereName($value)
 * @method static Builder<static>|Goods whereNo($value)
 * @method static Builder<static>|Goods wherePrice($value)
 * @method static Builder<static>|Goods whereQuotaNumber($value)
 * @method static Builder<static>|Goods whereSort($value)
 * @method static Builder<static>|Goods whereStatus($value)
 * @method static Builder<static>|Goods whereStatusDatetime($value)
 * @method static Builder<static>|Goods whereSubName($value)
 * @method static Builder<static>|Goods whereTotal($value)
 * @method static Builder<static>|Goods whereType($value)
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
    public const STATUS_ON_SALE = 1; // 上架
    public const STATUS_NOT_SALE = 0; // 下架

    protected $guarded = [];

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

    public function parameters(): HasMany
    {
        return $this->hasMany(GoodsParameter::class, 'goods_id', 'id');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(GoodsSku::class, 'goods_id', 'id');
    }

    public function scopeShow(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ON_SALE);
    }
}
