<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                          $id
 * @property int                          $order_id             订单ID
 * @property int                          $goods_id             商品ID
 * @property string                       $goods_no             商品编号
 * @property string                       $goods_name           商品名称
 * @property int                          $goods_number         商品数量
 * @property numeric                      $goods_price          商品价格
 * @property int                          $goods_integral       商品积分数量
 * @property numeric                      $goods_amount         商品总价
 * @property int                          $goods_total_integral 商品总积分
 * @property string                       $goods_unit           商品单位
 * @property int                          $goods_sku_id         商品 SKU ID
 * @property array<array-key, mixed>|null $goods_sku_value      商品规格值
 * @property Carbon|null                  $created_at
 * @property Carbon|null                  $updated_at
 * @property-read Goods $goods
 *
 * @method static Builder<static>|OrderDetail newModelQuery()
 * @method static Builder<static>|OrderDetail newQuery()
 * @method static Builder<static>|OrderDetail query()
 * @method static Builder<static>|OrderDetail whereCreatedAt($value)
 * @method static Builder<static>|OrderDetail whereGoodsAmount($value)
 * @method static Builder<static>|OrderDetail whereGoodsId($value)
 * @method static Builder<static>|OrderDetail whereGoodsIntegral($value)
 * @method static Builder<static>|OrderDetail whereGoodsName($value)
 * @method static Builder<static>|OrderDetail whereGoodsNo($value)
 * @method static Builder<static>|OrderDetail whereGoodsNumber($value)
 * @method static Builder<static>|OrderDetail whereGoodsPrice($value)
 * @method static Builder<static>|OrderDetail whereGoodsSkuId($value)
 * @method static Builder<static>|OrderDetail whereGoodsSkuValue($value)
 * @method static Builder<static>|OrderDetail whereGoodsTotalIntegral($value)
 * @method static Builder<static>|OrderDetail whereGoodsUnit($value)
 * @method static Builder<static>|OrderDetail whereId($value)
 * @method static Builder<static>|OrderDetail whereOrderId($value)
 * @method static Builder<static>|OrderDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OrderDetail extends BaseModel
{
    protected $guarded = [];

    public function goods(): BelongsTo
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }

    /**
     * @return string 获取商品规格字符串
     */
    public function skuValue(): string
    {
        $sku_value = '';

        if (empty($this->goods_sku_value)) {
            return $sku_value;
        }

        foreach ($this->goods_sku_value as $item) {
            $sku_value .= $item['key'].':'.$item['value'].';';
        }

        return $sku_value;
    }

    protected function casts(): array
    {
        return [
            'goods_price' => 'decimal:2',
            'goods_amount' => 'decimal:2',
            'goods_sku_value' => 'json',
        ];
    }
}
