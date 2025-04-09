<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $reason     原因
 * @property int                             $type       类型 0:退仅款；1退货退款
 * @property int                             $sort       排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ApplyRefundReason extends Model
{
    use DatetimeTrait;
    protected $guarded = [];
}
