<?php

namespace App\Models;




/**
 *
 *
 * @property int $id
 * @property string $name 红包名称
 * @property string $money 红包金额
 * @property int $number 红包数量
 * @property int $limit 限领数量
 * @property string|null $send_start_time 发放开始时间
 * @property string|null $send_end_time 发放结束时间
 * @property string|null $use_start_time 使用开始时间
 * @property string|null $use_end_time 使用结束时间
 * @property int $type 红包类型 0、不限制 1、限商品 2、限分类
 * @property string|null $type_values 红包类型限制的值
 * @property int $can_use_coupon 是否跟优惠券一起使用 0、不可以 1、可以
 * @property int $is_add 是否可以叠加 0、不可以 1、可以
 * @property string $min_amount 最小使用金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereCanUseCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereIsAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereMinAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereSendEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereSendStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereTypeValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereUseEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bonus whereUseStartTime($value)
 * @mixin \Eloquent
 */
class Bonus extends BaseModel
{


    // 限制商品
    public const TYPE_GOODS = 1;

    // 限制分类
    public const TYPE_CATEGORY = 2;
}
