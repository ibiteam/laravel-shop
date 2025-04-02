<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $order_delivery_id 发货订单ID
 * @property int         $order_detail_id   订单明细ID
 * @property int         $send_number       发货数量
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read OrderDetail|null $orderDetail
 *
 * @method static Builder<static>|OrderDeliveryItem newModelQuery()
 * @method static Builder<static>|OrderDeliveryItem newQuery()
 * @method static Builder<static>|OrderDeliveryItem query()
 * @method static Builder<static>|OrderDeliveryItem whereCreatedAt($value)
 * @method static Builder<static>|OrderDeliveryItem whereId($value)
 * @method static Builder<static>|OrderDeliveryItem whereOrderDeliveryId($value)
 * @method static Builder<static>|OrderDeliveryItem whereOrderDetailId($value)
 * @method static Builder<static>|OrderDeliveryItem whereSendNumber($value)
 * @method static Builder<static>|OrderDeliveryItem whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OrderDeliveryItem extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id', 'id');
    }
}
