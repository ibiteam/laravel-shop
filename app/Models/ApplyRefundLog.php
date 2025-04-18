<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $apply_refund_id      申请售后id
 * @property string|null                     $action_name          操作人
 * @property string|null                     $action               操作行为
 * @property int                             $type                 类型：0买方 1卖方
 * @property int                             $apply_refund_ship_id 售后物流ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ApplyRefund|null $applyRefund
 * @property-read \App\Models\ApplyRefundShip|null $applyRefundShip
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog whereActionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog whereApplyRefundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog whereApplyRefundShipId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ApplyRefundLog extends BaseModel
{


    // 类型 0:买方，1:卖方
    public const TYPE_BUYER = 0;
    public const TYPE_SELLER = 1;

    protected $guarded = [];

    public function applyRefund(): BelongsTo
    {
        return $this->belongsTo(ApplyRefund::class, 'apply_refund_id', 'id');
    }

    public function applyRefundShip(): BelongsTo
    {
        return $this->belongsTo(ApplyRefundShip::class, 'apply_refund_ship_id', 'id');
    }
}
