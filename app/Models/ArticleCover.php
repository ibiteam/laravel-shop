<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $img_url    图片地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCover newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCover newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCover query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCover whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCover whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCover whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCover whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ArticleCover extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
