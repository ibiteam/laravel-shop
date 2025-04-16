<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $apply_refund_id 申请退款ID
 * @property string                          $no              物流单号
 * @property int                             $ship_company_id 物流公司ID
 * @property string|null                     $phone           手机号
 * @property string|null                     $description     描述说明
 * @property string|null                     $certificate     凭证 ,号分割
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ApplyRefund|null $applyRefund
 * @property-read \App\Models\ShipCompany|null $shipCompany
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereApplyRefundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereCertificate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereShipCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundShip whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ApplyRefundShip extends Model
{
    use DatetimeTrait;
    protected $guarded = [];

    public function applyRefund(): BelongsTo
    {
        return $this->belongsTo(ApplyRefund::class, 'apply_refund_id', 'id');
    }

    public function shipCompany(): BelongsTo
    {
        return $this->belongsTo(ShipCompany::class, 'ship_company_id', 'id');
    }

    public function getCertificateAttribute($value): array
    {
        return $value ? explode(',', $value) : [];
    }
}
