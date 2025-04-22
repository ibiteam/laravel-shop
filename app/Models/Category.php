<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $parent_id   父级id
 * @property string      $name        名称
 * @property string|null $title       标题
 * @property string|null $keywords    关键词
 * @property string|null $description 描述
 * @property string|null $logo        图标
 * @property int         $sort        排序
 * @property int         $is_show     是否显示
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Category> $allChildren
 * @property-read int|null $all_children_count
 * @property-read Category|null $allParent
 * @property-read Collection<int, Goods> $goods
 * @property-read int|null $goods_count
 * @property-read Collection<int, Category> $showAllChildren
 * @property-read int|null $show_all_children_count
 *
 * @method static Builder<static>|Category newModelQuery()
 * @method static Builder<static>|Category newQuery()
 * @method static Builder<static>|Category query()
 * @method static Builder<static>|Category whereCreatedAt($value)
 * @method static Builder<static>|Category whereDescription($value)
 * @method static Builder<static>|Category whereId($value)
 * @method static Builder<static>|Category whereIsShow($value)
 * @method static Builder<static>|Category whereKeywords($value)
 * @method static Builder<static>|Category whereLogo($value)
 * @method static Builder<static>|Category whereName($value)
 * @method static Builder<static>|Category whereParentId($value)
 * @method static Builder<static>|Category whereSort($value)
 * @method static Builder<static>|Category whereTitle($value)
 * @method static Builder<static>|Category whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Category extends BaseModel
{
    // 是否显示 1是 0否
    public const IS_SHOW_YES = 1;
    public const IS_SHOW_NO = 0;

    protected $guarded = [];

    /**
     * 下级分类的所有父级.
     */
    public function allParent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id')->with('allParent');
    }

    /**
     * 父级分类的所有下级.
     */
    public function allChildren(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->with('allChildren');
    }

    public function goods(): HasMany
    {
        return $this->hasMany(Goods::class, 'category_id', 'id');
    }

    public function showAllChildren(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->where('is_show', self::IS_SHOW_YES)->with('showAllChildren');
    }
}
