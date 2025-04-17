<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $article_id 文章id
 * @property int                             $user_id    用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleView whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleView whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ArticleView extends Model
{
    use DatetimeTrait;
    protected $guarded = [];
}
