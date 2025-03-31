<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $name       分类名称
 * @property int                             $is_show    是否显示：1显示 2隐藏
 * @property int                             $sort       排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Router> $routers
 * @property-read int|null $routers_count
 *
 * @method static Builder<static>|RouterCategory newModelQuery()
 * @method static Builder<static>|RouterCategory newQuery()
 * @method static Builder<static>|RouterCategory query()
 * @method static Builder<static>|RouterCategory whereCreatedAt($value)
 * @method static Builder<static>|RouterCategory whereId($value)
 * @method static Builder<static>|RouterCategory whereIsShow($value)
 * @method static Builder<static>|RouterCategory whereName($value)
 * @method static Builder<static>|RouterCategory whereSort($value)
 * @method static Builder<static>|RouterCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class RouterCategory extends Model
{
    use DatetimeTrait;
    public const IS_SHOW_YES = 1; // 显示
    public const IS_SHOW_NO = 0; // 不显示
    protected $guarded = [];

    public function routers(): Builder|\Illuminate\Database\Eloquent\Relations\HasMany|RouterCategory
    {
        return $this->hasMany(Router::class, 'router_category_id', 'id');
    }
}
