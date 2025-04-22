<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property string                          $content    内容
 * @property int                             $type       类型 0:退仅款；1退货退款
 * @property int                             $sort       排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplyRefundReason withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ApplyRefundReason extends BaseModel
{
    use SoftDeletes;
    protected $guarded = [];
}
