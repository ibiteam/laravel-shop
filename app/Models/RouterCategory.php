<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                             $id
 * @property int                             $parent_id  父级id
 * @property string                          $name       名称
 * @property string                          $alias      别名
 * @property int                             $type       类型
 * @property string|null                     $page_name  页面名称
 * @property int                             $is_show    是否显示：1显示 2隐藏
 * @property int                             $sort       排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RouterCategory> $allChildren
 * @property-read int|null $all_children_count
 * @property-read RouterCategory|null $allParent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Router> $routers
 * @property-read int|null $routers_count
 *
 * @method static Builder<static>|RouterCategory newModelQuery()
 * @method static Builder<static>|RouterCategory newQuery()
 * @method static Builder<static>|RouterCategory query()
 * @method static Builder<static>|RouterCategory whereAlias($value)
 * @method static Builder<static>|RouterCategory whereCreatedAt($value)
 * @method static Builder<static>|RouterCategory whereId($value)
 * @method static Builder<static>|RouterCategory whereIsShow($value)
 * @method static Builder<static>|RouterCategory whereName($value)
 * @method static Builder<static>|RouterCategory wherePageName($value)
 * @method static Builder<static>|RouterCategory whereParentId($value)
 * @method static Builder<static>|RouterCategory whereSort($value)
 * @method static Builder<static>|RouterCategory whereType($value)
 * @method static Builder<static>|RouterCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class RouterCategory extends BaseModel
{


    // 是否显示
    public const IS_SHOW_YES = 1; // 显示
    public const IS_SHOW_NO = 0; // 不显示

    // 类型
    public const TYPE_LINK = 1; // 链接
    public const TYPE_MENU = 2; // 菜单

    protected $guarded = [];

    /**
     * 下级分类的所有父级.
     */
    public function allParent(): BelongsTo
    {
        return $this->belongsTo(RouterCategory::class, 'parent_id', 'id')->with('allParent');
    }

    /**
     * 父级分类的所有下级.
     */
    public function allChildren(): HasMany
    {
        return $this->hasMany(RouterCategory::class, 'parent_id', 'id')->with('allChildren');
    }

    public function routers(): HasMany
    {
        return $this->hasMany(Router::class, 'router_category_id', 'id');
    }
}
