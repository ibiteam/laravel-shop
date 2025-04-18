<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $coupon_id 优惠券id
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property string|null $used_time 使用时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon|null $coupon
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon whereUsedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCoupon whereUserId($value)
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @mixin \Eloquent
 */
class UserCoupon extends Model
{
    use DatetimeTrait;

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
