<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property int                             $article_category_id 分类id
 * @property string|null                     $title               标题
 * @property string|null                     $cover               封面
 * @property string|null                     $keywords            关键词
 * @property string|null                     $description         描述
 * @property string|null                     $author              作者
 * @property int                             $is_top              是否置顶
 * @property int                             $is_recommend        是否推荐
 * @property int                             $is_login            是否需要登录
 * @property int                             $is_show             是否显示
 * @property int                             $sort                排序
 * @property int                             $click_count         点击次数
 * @property string|null                     $file_url            文件路径
 * @property int                             $goods_category_id   商品分类id
 * @property int                             $admin_user_id       管理员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\AdminUser|null $adminUser
 * @property-read \App\Models\ArticleCategory|null $articleCategory
 * @property-read \App\Models\ArticleContent|null $articleContent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArticleView> $articleViews
 * @property-read int|null $article_views_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereArticleCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereClickCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereGoodsCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereIsLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereIsRecommend($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereIsTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Article extends Model
{
    use DatetimeTrait, SoftDeletes;

    // 是否显示 1是 0否
    public const IS_SHOW_YES = 1;
    public const IS_SHOW_NO = 0;

    // 是否置顶 1是 0否
    public const IS_TOP_YES = 1;
    public const IS_TOP_NO = 0;

    // 是否推荐 1是 0否
    public const IS_RECOMMEND_YES = 1;
    public const IS_RECOMMEND_NO = 0;

    // 是否需要登录 1是 0否
    public const IS_LOGIN_YES = 1;
    public const IS_LOGIN_NO = 0;

    protected $guarded = [];

    public function articleCategory(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id', 'id');
    }

    public function articleContent(): HasOne
    {
        return $this->hasOne(ArticleContent::class, 'article_id', 'id');
    }

    public function articleViews(): HasMany
    {
        return $this->hasMany(ArticleView::class, 'article_id', 'id');
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }
}
