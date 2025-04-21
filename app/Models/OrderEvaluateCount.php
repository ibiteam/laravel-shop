<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $goods_id            商品ID
 * @property int         $total               评论总数
 * @property int         $has_image_total     晒图数量
 * @property int         $rank_total          好评数量
 * @property int         $goods_rank_total    产品好数量
 * @property int         $price_rank_total    价格合理数量
 * @property int         $bus_rank_total      服务好数量
 * @property int         $delivery_rank_total 售后服务好数量
 * @property int         $service_rank_total  交货快数量
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|OrderEvaluateCount newModelQuery()
 * @method static Builder<static>|OrderEvaluateCount newQuery()
 * @method static Builder<static>|OrderEvaluateCount query()
 * @method static Builder<static>|OrderEvaluateCount whereBusRankTotal($value)
 * @method static Builder<static>|OrderEvaluateCount whereCreatedAt($value)
 * @method static Builder<static>|OrderEvaluateCount whereDeliveryRankTotal($value)
 * @method static Builder<static>|OrderEvaluateCount whereGoodsId($value)
 * @method static Builder<static>|OrderEvaluateCount whereGoodsRankTotal($value)
 * @method static Builder<static>|OrderEvaluateCount whereHasImageTotal($value)
 * @method static Builder<static>|OrderEvaluateCount whereId($value)
 * @method static Builder<static>|OrderEvaluateCount wherePriceRankTotal($value)
 * @method static Builder<static>|OrderEvaluateCount whereRankTotal($value)
 * @method static Builder<static>|OrderEvaluateCount whereServiceRankTotal($value)
 * @method static Builder<static>|OrderEvaluateCount whereTotal($value)
 * @method static Builder<static>|OrderEvaluateCount whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OrderEvaluateCount extends BaseModel
{

    public const TOTAL = 'total';
    public const HAS_IMAGE_TOTAL = 'has_image_total';
    public const RANK_TOTAL = 'rank_total';
    public const GOODS_RANK_TOTAL = 'goods_rank_total';
    public const PRICE_RANK_TOTAL = 'price_rank_total';
    public const BUS_RANK_TOTAL = 'bus_rank_total';
    public const DELIVERY_RANK_TOTAL = 'delivery_rank_total';
    public const SERVICE_RANK_TOTAL = 'service_rank_total';

    public array $tags = [
        self::TOTAL => '全部',
        self::HAS_IMAGE_TOTAL => '晒图',
        self::RANK_TOTAL => '好评',
        self::GOODS_RANK_TOTAL => '产品好',
        self::PRICE_RANK_TOTAL => '价格合理',
        self::BUS_RANK_TOTAL => '服务好',
        self::DELIVERY_RANK_TOTAL => '售后服务好',
        self::SERVICE_RANK_TOTAL => '交货快',
    ];

    protected $guarded = [];
}
