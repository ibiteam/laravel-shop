<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $role_id
 * @property string $model_type
 * @property int    $model_id
 * @property-read \App\Models\AdminUser|null $adminUser
 * @property-read \App\Models\Role $role
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelHasRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelHasRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelHasRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelHasRole whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelHasRole whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelHasRole whereRoleId($value)
 *
 * @mixin \Eloquent
 */
class ModelHasRole extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'model_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
