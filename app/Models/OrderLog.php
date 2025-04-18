<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $order_id        订单ID
 * @property string      $operate_type    类型
 * @property int         $operate_type_id 类型ID
 * @property int         $type            可见类型 1用户 2管理员
 * @property int         $order_status    订单状态
 * @property int         $pay_status      支付状态
 * @property int         $ship_status     发货状态
 * @property string      $comment         操作描述
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Eloquent $operateType
 * @property-read Order $order
 *
 * @method static Builder<static>|OrderLog newModelQuery()
 * @method static Builder<static>|OrderLog newQuery()
 * @method static Builder<static>|OrderLog query()
 * @method static Builder<static>|OrderLog whereComment($value)
 * @method static Builder<static>|OrderLog whereCreatedAt($value)
 * @method static Builder<static>|OrderLog whereId($value)
 * @method static Builder<static>|OrderLog whereOperateType($value)
 * @method static Builder<static>|OrderLog whereOperateTypeId($value)
 * @method static Builder<static>|OrderLog whereOrderId($value)
 * @method static Builder<static>|OrderLog whereOrderStatus($value)
 * @method static Builder<static>|OrderLog wherePayStatus($value)
 * @method static Builder<static>|OrderLog whereShipStatus($value)
 * @method static Builder<static>|OrderLog whereType($value)
 * @method static Builder<static>|OrderLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OrderLog extends BaseModel
{

    public const TYPE_USER = 1; // 可见类型：用户
    public const TYPE_ADMIN_USER = 2; // 可见类型：管理员

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function operateType(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'operate_type', 'operate_type_id');
    }
}
