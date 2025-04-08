<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $no              订单编号
 * @property int         $user_id         用户ID
 * @property string      $type            订单类型
 * @property int         $order_status    订单状态
 * @property int         $pay_status      支付状态
 * @property Carbon|null $paid_at         最新支付时间
 * @property int         $ship_status     发货状态
 * @property bool        $is_edit_address 是否已修改发货地址
 * @property Carbon|null $shipped_at      最新发货时间
 * @property Carbon|null $received_at     最新收货时间
 * @property int         $province_id     省份ID
 * @property int         $city_id         城市ID
 * @property int         $district_id     区县ID
 * @property string      $address         详细地址
 * @property string      $consignee       收货人
 * @property string      $phone           收货人手机号
 * @property numeric     $goods_amount    订单商品金额
 * @property numeric     $order_amount    订单总金额
 * @property numeric     $shipping_fee    运费
 * @property int         $integral        消耗积分数量
 * @property numeric     $coupon_amount   优惠劵金额
 * @property int         $coupon_id       优惠券ID
 * @property numeric     $money_paid      已支付金额
 * @property string      $remark          用户备注
 * @property string|null $cancel_reason   取消原因
 * @property string      $source          下单来源
 * @property string      $ip              下单IP
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Region|null $city
 * @property-read Collection<int, OrderDetail> $detail
 * @property-read int|null $detail_count
 * @property-read Region|null $district
 * @property-read Region|null $province
 * @property-read User $user
 *
 * @method static Builder<static>|Order newModelQuery()
 * @method static Builder<static>|Order newQuery()
 * @method static Builder<static>|Order query()
 * @method static Builder<static>|Order whereAddress($value)
 * @method static Builder<static>|Order whereCancelReason($value)
 * @method static Builder<static>|Order whereCityId($value)
 * @method static Builder<static>|Order whereConsignee($value)
 * @method static Builder<static>|Order whereCouponAmount($value)
 * @method static Builder<static>|Order whereCouponId($value)
 * @method static Builder<static>|Order whereCreatedAt($value)
 * @method static Builder<static>|Order whereDistrictId($value)
 * @method static Builder<static>|Order whereGoodsAmount($value)
 * @method static Builder<static>|Order whereId($value)
 * @method static Builder<static>|Order whereIntegral($value)
 * @method static Builder<static>|Order whereIp($value)
 * @method static Builder<static>|Order whereIsEditAddress($value)
 * @method static Builder<static>|Order whereMoneyPaid($value)
 * @method static Builder<static>|Order whereNo($value)
 * @method static Builder<static>|Order whereOrderAmount($value)
 * @method static Builder<static>|Order whereOrderStatus($value)
 * @method static Builder<static>|Order wherePaidAt($value)
 * @method static Builder<static>|Order wherePayStatus($value)
 * @method static Builder<static>|Order wherePhone($value)
 * @method static Builder<static>|Order whereProvinceId($value)
 * @method static Builder<static>|Order whereReceivedAt($value)
 * @method static Builder<static>|Order whereRemark($value)
 * @method static Builder<static>|Order whereShipStatus($value)
 * @method static Builder<static>|Order whereShippedAt($value)
 * @method static Builder<static>|Order whereShippingFee($value)
 * @method static Builder<static>|Order whereSource($value)
 * @method static Builder<static>|Order whereType($value)
 * @method static Builder<static>|Order whereUpdatedAt($value)
 * @method static Builder<static>|Order whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Order extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'province_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'city_id', 'id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'district_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'goods_amount' => 'decimal:2',
            'order_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'coupon_amount' => 'decimal:2',
            'money_paid' => 'decimal:2',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'received_at' => 'datetime',
            'is_edit_address' => 'boolean',
        ];
    }
}
