<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name 优惠券名称
 * @property string $money 优惠券金额
 * @property int $number 优惠券数量 0 不限量
 * @property int $limit 每人限领数量 0 不限制
 * @property int $style 优惠券类型 1、满减券
 * @property int $type 优惠券限制类型 0、无限制 1、限商品 2、限分类
 * @property string $type_values 限制类型的值
 * @property string $min_amount 最小使用金额
 * @property string|null $start_time 开始时间
 * @property string|null $end_time 结束时间
 * @property int $send_start_time 发放开始时间
 * @property int $send_end_time 发放结束时间
 * @property int $is_add 是否可以叠加 1、可以叠加 0、不可以叠加
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereIsAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereMinAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereSendEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereSendStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereTypeValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    use DatetimeTrait;

    // 限制商品
    public const TYPE_GOODS = 1;

    // 限制分类
    public const TYPE_CATEGORY = 2;
}
