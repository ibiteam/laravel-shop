<?php

namespace App\Models;

use App\Observers\OrderEvaluateObserver;
use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                          $id
 * @property int                          $order_id        订单ID
 * @property int                          $order_detail_id 订单明细ID
 * @property int                          $user_id         用户ID
 * @property int                          $goods_id        商品ID
 * @property bool                         $is_anonymous    是否匿名
 * @property int                          $status          状态：0未处理 1通过 2驳回
 * @property string                       $comment         评价内容
 * @property array<array-key, mixed>|null $images          评价图片
 * @property Carbon                       $comment_at      评价时间
 * @property int                          $rank            综合评分
 * @property int                          $goods_rank      商品评分
 * @property int                          $price_rank      价格评分
 * @property int                          $bus_rank        商家服务评分
 * @property int                          $delivery_rank   交货速度评分
 * @property int                          $service_rank    服务评分
 * @property Carbon|null                  $created_at
 * @property Carbon|null                  $updated_at
 * @property-read Goods|null $goods
 * @property-read User $user
 *
 * @method static Builder<static>|OrderEvaluate newModelQuery()
 * @method static Builder<static>|OrderEvaluate newQuery()
 * @method static Builder<static>|OrderEvaluate query()
 * @method static Builder<static>|OrderEvaluate whereBusRank($value)
 * @method static Builder<static>|OrderEvaluate whereComment($value)
 * @method static Builder<static>|OrderEvaluate whereCommentAt($value)
 * @method static Builder<static>|OrderEvaluate whereCreatedAt($value)
 * @method static Builder<static>|OrderEvaluate whereDeliveryRank($value)
 * @method static Builder<static>|OrderEvaluate whereGoodsId($value)
 * @method static Builder<static>|OrderEvaluate whereGoodsRank($value)
 * @method static Builder<static>|OrderEvaluate whereId($value)
 * @method static Builder<static>|OrderEvaluate whereImages($value)
 * @method static Builder<static>|OrderEvaluate whereIsAnonymous($value)
 * @method static Builder<static>|OrderEvaluate whereOrderDetailId($value)
 * @method static Builder<static>|OrderEvaluate whereOrderId($value)
 * @method static Builder<static>|OrderEvaluate wherePriceRank($value)
 * @method static Builder<static>|OrderEvaluate whereRank($value)
 * @method static Builder<static>|OrderEvaluate whereServiceRank($value)
 * @method static Builder<static>|OrderEvaluate whereStatus($value)
 * @method static Builder<static>|OrderEvaluate whereUpdatedAt($value)
 * @method static Builder<static>|OrderEvaluate whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(OrderEvaluateObserver::class)]
class OrderEvaluate extends Model
{
    use DatetimeTrait;
    public const STATUS_SUCCESS = 1; // 通过
    public const STATUS_REJECT = 2; // 驳回
    public const STATUS_WAIT = 0; // 待处理

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function goods(): BelongsTo
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'comment_at' => 'datetime',
            'images' => 'json',
        ];
    }
}
