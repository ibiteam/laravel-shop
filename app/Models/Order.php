<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $order_sn        订单编号
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
 * @property string      $payment_method  支付方式
 * @property numeric     $money_paid      已支付金额
 * @property string      $remark          用户备注
 * @property string|null $cancel_reason   取消原因
 * @property string      $source          下单来源
 * @property string      $ip              下单IP
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Region|null $city
 * @property-read Collection<int, OrderDetail> $detail
 * @property-read int|null $detail_count
 * @property-read Region|null $district
 * @property-read Collection<int, OrderEvaluate> $evaluate
 * @property-read int|null $evaluate_count
 * @property-read Collection<int, OrderDelivery> $orderDelivery
 * @property-read int|null $order_delivery_count
 * @property-read Region|null $province
 * @property-read Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read User $user
 *
 * @method static Builder<static>|Order newModelQuery()
 * @method static Builder<static>|Order newQuery()
 * @method static Builder<static>|Order onlyTrashed()
 * @method static Builder<static>|Order query()
 * @method static Builder<static>|Order searchComplete()
 * @method static Builder<static>|Order searchWaitEvaluate(array $evaluate_ids = [])
 * @method static Builder<static>|Order searchWaitPay()
 * @method static Builder<static>|Order searchWaitReceive()
 * @method static Builder<static>|Order searchWaitShip()
 * @method static Builder<static>|Order whereAddress($value)
 * @method static Builder<static>|Order whereCancelReason($value)
 * @method static Builder<static>|Order whereCityId($value)
 * @method static Builder<static>|Order whereConsignee($value)
 * @method static Builder<static>|Order whereCouponAmount($value)
 * @method static Builder<static>|Order whereCouponId($value)
 * @method static Builder<static>|Order whereCreatedAt($value)
 * @method static Builder<static>|Order whereDeletedAt($value)
 * @method static Builder<static>|Order whereDistrictId($value)
 * @method static Builder<static>|Order whereGoodsAmount($value)
 * @method static Builder<static>|Order whereId($value)
 * @method static Builder<static>|Order whereIntegral($value)
 * @method static Builder<static>|Order whereIp($value)
 * @method static Builder<static>|Order whereIsEditAddress($value)
 * @method static Builder<static>|Order whereMoneyPaid($value)
 * @method static Builder<static>|Order whereOrderAmount($value)
 * @method static Builder<static>|Order whereOrderSn($value)
 * @method static Builder<static>|Order whereOrderStatus($value)
 * @method static Builder<static>|Order wherePaidAt($value)
 * @method static Builder<static>|Order wherePayStatus($value)
 * @method static Builder<static>|Order wherePaymentMethod($value)
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
 * @method static Builder<static>|Order withTrashed()
 * @method static Builder<static>|Order withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Order extends BaseModel
{
    use DatetimeTrait, SoftDeletes;
    public const PAYMENT_METHOD_ONLINE = 'online'; // 在线支付

    public static array $paymentMethodMap = [
        self::PAYMENT_METHOD_ONLINE => '在线支付',
    ];

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

    public function orderDelivery(): HasMany
    {
        return $this->hasMany(OrderDelivery::class, 'order_id', 'id');
    }

    public function evaluate(): HasMany
    {
        return $this->hasMany(OrderEvaluate::class, 'order_id', 'id');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'typeInfo', 'type', 'type_id');
    }

    /**
     * 待付款查询作用域
     */
    public function scopeSearchWaitPay(Builder $query): Builder
    {
        return $query
            ->where('order_status', OrderStatusEnum::CONFIRMED->value)
            ->where('pay_status', PayStatusEnum::PAY_WAIT->value);
    }

    /**
     * 待发货查询作用域
     */
    public function scopeSearchWaitShip(Builder $query): Builder
    {
        return $query
            ->where('order_status', OrderStatusEnum::CONFIRMED->value)
            ->where('pay_status', PayStatusEnum::PAYED->value)
            ->where('ship_status', ShippingStatusEnum::UNSHIPPED->value);
    }

    /**
     * 待评价查询作用域
     */
    public function scopeSearchWaitEvaluate(Builder $query, array $evaluate_ids = []): Builder
    {
        return $query
            ->where('order_status', OrderStatusEnum::CONFIRMED->value)
            ->where('pay_status', PayStatusEnum::PAYED->value)
            ->where('ship_status', ShippingStatusEnum::RECEIVED->value)
            ->whereNotIn('id', $evaluate_ids);
    }

    /**
     * 待收货查询作用域
     */
    public function scopeSearchWaitReceive(Builder $query): Builder
    {
        return $query
            ->where('order_status', OrderStatusEnum::CONFIRMED->value)
            ->where('pay_status', PayStatusEnum::PAYED->value)
            ->where('ship_status', ShippingStatusEnum::SHIPPED->value);
    }

    /**
     * 订单完成查询作用域
     */
    public function scopeSearchComplete(Builder $query): Builder
    {
        return $query
            ->where('order_status', OrderStatusEnum::CONFIRMED->value)
            ->where('pay_status', PayStatusEnum::PAYED->value)
            ->where('ship_status', ShippingStatusEnum::RECEIVED->value);
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
