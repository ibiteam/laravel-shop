<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $delivery_no     运单号
 * @property int         $order_id        订单ID
 * @property int         $ship_company_id 快递公司ID
 * @property string      $ship_no         快递单号
 * @property int         $status          状态
 * @property Carbon      $shipped_at      发货时间
 * @property Carbon|null $received_at     收货时间
 * @property string      $remark          备注
 * @property int         $admin_user_id   操作人ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AdminUser|null $adminUser
 * @property-read Order $order
 * @property-read ShipCompany $shipCompany
 *
 * @method static Builder<static>|OrderDelivery newModelQuery()
 * @method static Builder<static>|OrderDelivery newQuery()
 * @method static Builder<static>|OrderDelivery query()
 * @method static Builder<static>|OrderDelivery whereAdminUserId($value)
 * @method static Builder<static>|OrderDelivery whereCreatedAt($value)
 * @method static Builder<static>|OrderDelivery whereDeliveryNo($value)
 * @method static Builder<static>|OrderDelivery whereId($value)
 * @method static Builder<static>|OrderDelivery whereOrderId($value)
 * @method static Builder<static>|OrderDelivery whereReceivedAt($value)
 * @method static Builder<static>|OrderDelivery whereRemark($value)
 * @method static Builder<static>|OrderDelivery whereShipCompanyId($value)
 * @method static Builder<static>|OrderDelivery whereShipNo($value)
 * @method static Builder<static>|OrderDelivery whereShippedAt($value)
 * @method static Builder<static>|OrderDelivery whereStatus($value)
 * @method static Builder<static>|OrderDelivery whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OrderDelivery extends Model
{
    use DatetimeTrait;
    public const STATUS_SUCCESS = 1; // 已收货
    public const STATUS_WAIT = 0; // 待收货

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function shipCompany(): BelongsTo
    {
        return $this->belongsTo(ShipCompany::class, 'ship_company_id', 'id');
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'shipped_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }
}
