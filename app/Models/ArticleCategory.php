<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                             $id
 * @property int                             $parent_id   父级id
 * @property string                          $name        名称
 * @property string|null                     $alias       别名
 * @property string|null                     $title       标题
 * @property string|null                     $keywords    关键词
 * @property string|null                     $description 描述
 * @property int                             $type        类型
 * @property int                             $sort        排序
 * @property int                             $is_show     是否显示
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ArticleCategory> $allChildren
 * @property-read int|null $all_children_count
 * @property-read ArticleCategory|null $allParent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ArticleCategory extends BaseModel
{


    // 是否显示
    public const IS_SHOW_YES = 1; // 显示
    public const IS_SHOW_NO = 0; // 不显示

    // 类型
    public const TYPE_COMMON = 1;   // 普通分类（仅限文章、资讯选择此类型）
    public const TYPE_SYSTEM = 2;   // 系统分类（仅限帮助中心、公共分类选择此类型）

    protected $guarded = [];

    /**
     * 下级分类的所有父级.
     */
    public function allParent(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'parent_id', 'id')->with('allParent');
    }

    /**
     * 父级分类的所有下级.
     */
    public function allChildren(): HasMany
    {
        return $this->hasMany(ArticleCategory::class, 'parent_id', 'id')->with('allChildren');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'article_category_id', 'id');
    }
}
