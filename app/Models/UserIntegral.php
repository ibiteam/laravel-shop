<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $user_id    用户id
 * @property int         $number     积分数量
 * @property int         $type       积分类型 1、增加 2、减少
 * @property string|null $desc       积分描述
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|UserIntegral newModelQuery()
 * @method static Builder<static>|UserIntegral newQuery()
 * @method static Builder<static>|UserIntegral query()
 * @method static Builder<static>|UserIntegral whereCreatedAt($value)
 * @method static Builder<static>|UserIntegral whereDesc($value)
 * @method static Builder<static>|UserIntegral whereId($value)
 * @method static Builder<static>|UserIntegral whereNumber($value)
 * @method static Builder<static>|UserIntegral whereType($value)
 * @method static Builder<static>|UserIntegral whereUpdatedAt($value)
 * @method static Builder<static>|UserIntegral whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserIntegral extends Model
{
    use DatetimeTrait;
    public const TYPE_INCREMENT = 1; // 增加
    public const TYPE_DECREMENT = 2; // 减少

    protected $guarded = [];
}
