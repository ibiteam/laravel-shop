<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $admin_user_id 管理员id
 * @property string|null                     $description   操作描述
 * @property int                             $type          类型
 * @property string|null                     $table         表名
 * @property int                             $table_id      表名主键ID
 * @property string                          $ip            操作IP
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUser $adminUser
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AdminOperationLog extends Model
{
    use DatetimeTrait;

    // 日志类型
    public const BASIC_OPERATIONS = 0; // 基本操作日志
    public const SELLER_ENTER_CHECK = 1; // 商家入驻审核日志

    protected $guarded = [];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }
}
