<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;

/**
 * @property int         $id
 * @property int         $category_id     分类ID
 * @property string      $no              商品编号
 * @property string      $name            商品标题
 * @property string|null $sub_name        商品副标题
 * @property string|null $label           商品标签
 * @property string      $image           商品主图
 * @property string|null $unit            商品单位
 * @property numeric     $price           商品价格
 * @property int         $integral        积分
 * @property int         $total           商品库存
 * @property int         $sales_volume    销量
 * @property int         $type            库存类型 1下单减库存 2付款减库存
 * @property int         $status          上下架状态 1上架 0下架
 * @property Carbon|null $status_datetime 上下架时间
 * @property int         $can_quota       是否限购 1限购 0不限购
 * @property int         $quota_number    限购数量
 * @property int         $sort            排序，值越大越靠前
 * @property string|null $video           视频地址
 * @property int         $video_duration  视频时长
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Category $category
 * @property-read GoodsDetail|null $detail
 * @property-read Collection<int, GoodsImage> $images
 * @property-read int|null $images_count
 * @property-read Collection<int, GoodsParameter> $parameters
 * @property-read int|null $parameters_count
 * @property-read Collection<int, GoodsSku> $skus
 * @property-read int|null $skus_count
 * @property-read Collection<int, GoodsSpecValue> $specValues
 * @property-read int|null $spec_values_count
 *
 * @method static Builder<static>|Goods newModelQuery()
 * @method static Builder<static>|Goods newQuery()
 * @method static Builder<static>|Goods onlyTrashed()
 * @method static Builder<static>|Goods query()
 * @method static Builder<static>|Goods show()
 * @method static Builder<static>|Goods whereCanQuota($value)
 * @method static Builder<static>|Goods whereCategoryId($value)
 * @method static Builder<static>|Goods whereCreatedAt($value)
 * @method static Builder<static>|Goods whereDeletedAt($value)
 * @method static Builder<static>|Goods whereId($value)
 * @method static Builder<static>|Goods whereImage($value)
 * @method static Builder<static>|Goods whereIntegral($value)
 * @method static Builder<static>|Goods whereLabel($value)
 * @method static Builder<static>|Goods whereName($value)
 * @method static Builder<static>|Goods whereNo($value)
 * @method static Builder<static>|Goods wherePrice($value)
 * @method static Builder<static>|Goods whereQuotaNumber($value)
 * @method static Builder<static>|Goods whereSalesVolume($value)
 * @method static Builder<static>|Goods whereSort($value)
 * @method static Builder<static>|Goods whereStatus($value)
 * @method static Builder<static>|Goods whereStatusDatetime($value)
 * @method static Builder<static>|Goods whereSubName($value)
 * @method static Builder<static>|Goods whereTotal($value)
 * @method static Builder<static>|Goods whereType($value)
 * @method static Builder<static>|Goods whereUnit($value)
 * @method static Builder<static>|Goods whereUpdatedAt($value)
 * @method static Builder<static>|Goods whereVideo($value)
 * @method static Builder<static>|Goods whereVideoDuration($value)
 * @method static Builder<static>|Goods withTrashed()
 * @method static Builder<static>|Goods withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Goods extends BaseModel
{
    use Searchable, SoftDeletes;
    public const STATUS_ON_SALE = 1; // 上架
    public const STATUS_NOT_SALE = 0; // 下架
    public const QUOTA = 1; // 限购
    public const NOT_QUOTA = 0; // 不限购
    public const TYPE_DONE_ORDER = 1; // 下单减库存
    public const TYPE_PAY_ORDER = 2; // 支付订单减库存

    // 排序对应字段
    public static $sorts = [
        AppDecorationItem::SORT_SALES => 'sales_volume DESC',
        AppDecorationItem::SORT_LOW_PRICE => 'price ASC',
        AppDecorationItem::SORT_NEW_PRODUCT => 'created_at DESC',
    ];

    protected $guarded = [];

    // 配置模型索引
    public function searchableAs(): string
    {
        return 'goods_index';
    }

    // 配置可搜索数据
    public function toSearchableArray(): array
    {
        return $this->toArray();
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

    public function parameters(): HasMany
    {
        return $this->hasMany(GoodsParameter::class, 'goods_id', 'id');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(GoodsSku::class, 'goods_id', 'id');
    }

    public function specValues(): HasMany
    {
        return $this->hasMany(GoodsSpecValue::class, 'goods_id', 'id');
    }

    public function scopeShow(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ON_SALE);
    }

    public function decrementStock(int $buy_number): void
    {
        $this->decrement('total', $buy_number);
        $this->increment('sales_volume', $buy_number);
    }

    public function incrementStock(int $buy_number): void
    {
        $this->increment('total', $buy_number);

        if ($this->sales_volume >= $buy_number) {
            $this->decrement('sales_volume', $buy_number);
        } else {
            $this->decrement('sales_volume', $this->sales_volume);
        }
    }

    public function isPayDecrementStock(): bool
    {
        return $this->type === self::TYPE_PAY_ORDER;
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status_datetime' => 'datetime',
        ];
    }
}
