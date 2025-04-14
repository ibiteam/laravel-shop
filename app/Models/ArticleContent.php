<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property int                             $article_id 文章id
 * @property string|null                     $content    文章内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleContent whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleContent whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ArticleContent extends Model
{
    use DatetimeTrait;
    protected $guarded = [];
}
