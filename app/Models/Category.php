<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                             $id
 * @property int                             $parent_id   父级id
 * @property string                          $name        名称
 * @property string|null                     $title       标题
 * @property string|null                     $keywords    关键词
 * @property string|null                     $description 描述
 * @property string|null                     $logo        图标
 * @property int                             $sort        排序
 * @property int                             $is_show     是否显示
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $allChildren
 * @property-read int|null $all_children_count
 * @property-read Category|null $allParent
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Category extends Model
{
    use DatetimeTrait;

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
}
