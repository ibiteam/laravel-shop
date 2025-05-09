<?php

namespace App\Models;




/**
 * @property int                             $id
 * @property int                             $goods_id   商品ID
 * @property string                          $sku_value  属性值ID“|”分割
 * @property string                          $price      价格
 * @property int                             $integral   积分
 * @property int                             $number     库存
 * @property int                             $is_show    是否展示 1展示 0不展示
 * @property int                             $sort       排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereSkuValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSku whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class GoodsSku extends BaseModel
{

    public const SHOW = 1; // 展示
    public const NOT_SHOW = 0; // 不展示

    protected $guarded = [];

    public function explodeSkuValue(): array
    {
        return explode('|', $this->sku_value);
    }

    public function implodeSkuValue(array $sku_value): string
    {
        return implode('|', $sku_value);
    }

    public function decrementStock(int $buy_number): void
    {
        $this->decrement('number', $buy_number);
    }

    public function incrementStock(int $buy_number): void
    {
        $this->increment('number', $buy_number);
    }
}
