<?php

namespace App\Models;




/**
 *
 *
 * @property int $id
 * @property int $bonus_id 红包id
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property string|null $used_time 使用时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bonus|null $bonus
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus whereBonusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus whereUsedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBonus whereUserId($value)
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @mixin \Eloquent
 */
class UserBonus extends BaseModel
{


    public function bonus()
    {
        return $this->belongsTo(Bonus::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
