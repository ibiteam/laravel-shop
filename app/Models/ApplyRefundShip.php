<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $apply_refund_id 申请退款ID
 * @property string                          $no              物流单号
 * @property int                             $ship_company_id 物流公司ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereApplyRefundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereShipCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ApplyRefundShip extends Model
{
    use DatetimeTrait;
    protected $guarded = [];
}
