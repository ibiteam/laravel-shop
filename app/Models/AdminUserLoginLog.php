<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $admin_user_id 管理员ID
 * @property string                          $type          登录类型
 * @property int                             $status        登录状态
 * @property string                          $description   登录描述
 * @property string                          $ip            登录IP
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUser|null $adminUser
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUserLoginLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AdminUserLoginLog extends BaseModel
{

    public const STATUS_SUCCESS = 1; // 登录成功
    public const STATUS_FAILED = 0; // 登录失败


    public const TYPE_PASSWORD = 'password'; // 账号密码登录

    protected $guarded = [];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }
}
