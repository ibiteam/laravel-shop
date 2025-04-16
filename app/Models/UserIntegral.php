<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $number 积分数量
 * @property int $type 积分类型 1、增加 2、减少
 * @property string|null $desc 积分描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserIntegral whereUserId($value)
 * @mixin \Eloquent
 */
class UserIntegral extends Model
{
    use DatetimeTrait;
}
